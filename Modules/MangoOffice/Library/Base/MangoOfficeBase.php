<?php

namespace Modules\MangoOffice\Library\Base;

use Modules\MangoOffice\Library\Excepiion\ApiException;
use Modules\MangoOffice\Library\MangoOfficeError;
use Illuminate\Http\Request;
use Modules\MangoOffice\Library\Result\MangoOfficeStatField;
use Carbon\Carbon;
use Modules\MangoOffice\Library\Excepiion\EmptyResultException;

class MangoOfficeBase
{

    const REQUEST_METHOD_GET = 'GET';
    const REQUEST_METHOD_POST = 'POST';
    const REQUEST_METHOD_PUT = 'PUT';
    const REQUEST_METHOD_DELETE = 'DELETE';

    public $baseUrl;

    public $vpbxApiKey;

    public $vpbxApiSalt;

    /**
     * @var null|Request
     */
    protected $request = null;

    public function __construct()
    {
        $this->baseUrl = env('MANGO_BASE_URL');
        $this->vpbxApiKey = env('MANGO_API_KEY');
        $this->vpbxApiSalt = env('MANGO_API_SALT');
    }

    function checkSalt($json, $sign)
    {
        $this->checkKey();
        $test = $this->getSign($json);

        return $sign == $test;
    }

    /**
     * Получение подписи по данным
     *
     * @param string|array $data
     *
     * @return string
     */
    protected function getSign($data)
    {
        if (is_array($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return hash('sha256', $this->vpbxApiKey.$data.$this->vpbxApiSalt);
    }

    /**
     * Проверка на наличие ключей приложения и шифрования
     *
     * @throws \Exception
     */
    protected function checkKey()
    {
        if (is_null($this->vpbxApiKey) || is_null($this->vpbxApiSalt)) {
            throw new ApiException('Необходимо задать API key и ключ шифрования');
        }
    }

    public function request($url, array $params = [], $method = self::REQUEST_METHOD_GET, $errorHandler = null)
    {
        $cache = $this->_getCache($url, $params);

        if ($cache) {
            return $cache;
        } else {
            $info = sprintf('%s %s %s', $method, $url, json_encode($params));

            $data = $this->_sendRequest($url, $params, $method, $errorHandler);

            if ($data !== false) {
                $this->_setCache($url, $params, $data);
            }
        }

        return $data;
    }

    function getMethodData()
    {
        $this->checkKey();

        $sign = $this->getFromRequest('sign');
        $json = $this->getFromRequest('json');

        if (!($this->getFromRequest('vpbx_api_key') == $this->vpbxApiKey)) {
            return MangoOfficeError::error(3105);
        }
        if (!$this->checkSalt($json, $sign)) {
            return MangoOfficeError::error(3102);
        }

        if (self::REQUEST_METHOD_POST != $this->request->getMethod()) {
            return MangoOfficeError::error(3101);
        }

        return json_decode($json);
    }

    protected function getFromRequest($key, $default = null)
    {
        if (is_null($this->request)) {
            $this->request = Request::createFromGlobals();
        }

        return $this->request->get($key, $default);
    }

    /**
     * @param      $command_id
     * @param      $from - (внутренний номер) идентификатор сотрудника ВАТС. Обязательное поле. Если у сотрудника ВАТС
     *        нет идентификатора (внутреннего номера), он не сможет выполнять команду инициирования вызова.
     * @param      $to_number - номер вызываемого абонента (строка не более 128 байт). Может быть
     * идентификатором сотрудника ВАТС, внутренним номером группы операторов ВАТС
     * или любым другим номером.
     * @param null $number - номер вызывающего абонента (строка не более 128 байт).
     * Опциональный параметр. Поле следует использовать в случае, если вызов
     * должен быть инициирован с номера, отличного от номера по умолчанию
     * сотрудника ВАТС. В качестве значения можно указывать: SIP из PSTN номера,
     * но нельзя указывать внутренние номера и номера групп ВАТС. К номеру будут
     * применены правила преобразования номеров ВАТС. Если будет указан номер,
     * отличный от номеров сотрудника ВАТС, которому соответствует поле
     * "extension", на время вызова этот номер будет считаться номером сотрудника.
     */
    function sendCall($from, $to_number, $number = null, $command_id = null)
    {
        $data = [
            'from' => [
                'extension' => $from,
            ],
            'to_number' => $to_number,
        ];
        if (!is_null($number)) {
            $data['from']['number'] = $number;
        }
        $response  = $this->putCmd('commands/callback', $data, $command_id);
        return $response['data'];
    }

    /**
     * Функция отправки команды на сервер манго-офиса
     *
     * @param       $method
     * @param array $data
     * @param null $command_id
     * @param boolean $includeResponseHeader
     *
     * @return mixed
     */
    protected function putCmd(
        $method,
        array $data,
        $command_id = null,
        $includeHeader = false
    )
    {
        $headers = [];
        if (false !== $command_id) {
            if (is_null($command_id)) {
                $command_id = md5($data.$this->getSign($data));
            }
            $data['command_id'] = $command_id;
        }
        $post = [
            'vpbx_api_key' => $this->vpbxApiKey,
            'sign' => $this->getSign($data),
            'json' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];

        $url = $this->baseUrl.$method;

        $query = http_build_query($post);
        if (0) {

            /**
             * FILE GET CONTENTS
             */
            $opts = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $query,
                ],
            ];
            $context = stream_context_create($opts);
            $data = @file_get_contents($url, false, $context);
            if (!$data) {
                return false;
            }
        } else {
            /**
             * CURL
             */
            $i = 0;
            do {
                if ($curl = curl_init()) {
                    sleep(1);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
                    curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
                    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
                    curl_setopt($curl, CURLOPT_HEADER, true);
                    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
                    $data = curl_exec($curl);
                    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
                    $curl_info = curl_getinfo($curl);

                    $code = isset($curl_info['http_code'])?$curl_info['http_code']:null;
                    $headers = $this->get_headers_from_curl_response($data);
                    $data = substr($data, $header_size);
                    curl_close($curl);
                    $i++;
                }
            }
            while($code == 204 && $i < 5);
        }

        $ret = json_decode($data);
        if ($ret instanceof \stdClass
            && isset($ret->status)
            && $ret->status != 200) {
            throw new ApiException($ret->message, $ret->status);
        }
        if (!empty($ret)) {
            return ['data' => $ret, 'headers' => $headers];
        }

        return ['data' => $data, 'headers' => $headers];
    }

    function get_headers_from_curl_response($response)
    {
        $headers = array();

        $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line)
            if ($i === 0)
                $headers['http_code'] = $line;
            else
            {
                list ($key, $value) = explode(': ', $line);

                $headers[$key] = $value;
            }

        return $headers;
    }


    /**
     * Завершить вызов
     *
     * @param $command_id - идентификатор команды (строка не более 128 байт).
     * @param $call_id - идентификатор вызова, который необходимо завершить.
     *
     * @return mixed
     */
    function sendCallHangup($command_id, $call_id)
    {
        $data = [
            'call_id' => $call_id,
        ];
        $response = $this->putCmd('commands/call/hangup', $data, $command_id);
        return $response['data'];
    }
    /**
     * Получение статистики
     *
     * @param      $date_from - предоставить статистику с указанного времени.
     * @param      $date_to - предоставить статистику по указанное время.
     * @param null $from - идентификатор сотрудника ВАТС для вызывающего абонента
     * @param null $from_number - номер вызывающего абонента (строка)
     * @param null $to -  идентификатор сотрудника ВАТС для вызываемого абонента
     * @param null $to_number - номер вызываемого абонента (строка)
     * @param null $fields - Позволяет указать какие поля (см. список
     * возможных полей ниже)и в каком порядке необходимо включить в выгрузку. Значение
     * по умолчанию: ["records", "start", "finish", "from_extension", "from_number",
     * "to_extension", "to_number", "disconnect_reason"]
     * @param null $request_id - идентификатор запроса (строка не более 128 байт)
     *
     * @return array|bool
     */
    function getStat(
        $date_from,
        $date_to,
        $from = 0,
        $from_number = null,
        $to = null,
        $to_number = null,
        $fields = null,
        $request_id = null,
        $from_extension = null,
        $to_extension = null
    ) {

        $data = [
            'date_from' => Carbon::createFromFormat('Y.m.d H:i', $date_from. ' 00:00')->timestamp,
            'date_to' => Carbon::createFromFormat('Y.m.d H:i', $date_to. ' 23:59')->timestamp,
        ];
        if (!is_null($from)) {
            $data['from']['extension'] = $from;
        }
        if (!is_null($from_number)) {
            $data['from']['number'] = $from_number;
        }
        if (!is_null($to)) {
            $data['to']['extension'] = $to;
        }
        if (!is_null($to_number)) {
            $data['to']['number'] = $to_number;
        }
        if (is_null($request_id)) {
            $request_id = md5($this->getSign($data));
        }

        if ($from_extension) {
            $data['from']['extension'] = $from_extension;
        }

        if ($to_extension) {
            $data['to']['extension'] = $to_extension;
        }

        if (is_null($fields)) {
            $fields = [
                'records',
                'start',
                'finish',
                'from_extension',
                'from_number',
                'to_extension',
                'to_number',
                'disconnect_reason',
                'entry_id',
            ];
        }
        $data['fields'] = implode(',', (array)$fields);
        $data['request_id'] = $request_id;
        $response = $this->putCmd('stats/request', $data, false);
        $stat_key_data = $response['data'];

        if (!$stat_key_data->key) {
            return false;
        }

        $data = [
            'key' => $stat_key_data->key,
            'request_id' => $request_id
        ];

        $response = $this->putCmd('stats/result', $data, false);
        $info = $response['data'];
        if (isset($info->code)) {
            return MangoOfficeError::error($info->code);
        }

        return $this->getCsv($info, $fields);
    }

    /**
     * Преобразование CSV файла в массив из данных переданных в $fields
     *
     * @param $info
     * @param $fields
     *
     * @return array
     */
    protected function getCsv($info, $fields)
    {
        $ret = [];
        $lines = explode("\n", $info);

        if (count($lines)) {
            foreach ($lines as $line) {
                if ($line) {
                    $values = explode(';', $line);
                    if (count($values)) {
                        $values = array_map(
                            function ($v) {
                                return trim(trim(trim($v), '['), ']');
                            },
                            $values
                        );
                        $data = array_combine(array_values($fields), array_values($values));
                        $filed = new MangoOfficeStatField($data);
                        $ret[] = $filed->toArray();
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * @param $recordingId
     * @return bool|mixed|string
     * @throws ApiException
     * @throws EmptyResultException
     */
    public function getRecord($recordID)
    {
        $data = array(
            "recording_id" => $recordID,
            "action" => "download" // <- скачать ("play" - проиграть)
        );

        $response = $this->putCmd('queries/recording/post/', $data, false);
        $data = $response['data'];
        if (isset($data->code)) {
            return MangoOfficeError::error($data->code);
        }

        if (!isset($response['headers']['Location'])) {
            throw new ApiException('Not file');
        }
        return $response['headers']['Location'];

    }
}