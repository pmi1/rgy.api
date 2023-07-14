<?php

namespace Modules\MangoOffice\Library\Result;

use Carbon\Carbon;

/**
 * Class MangoOfficeFieldData
 *
 * @property array  $records
 * @property Carbon $start
 * @property Carbon $finish
 * @property string $from_extension
 * @property string $from_number
 * @property string $to_extension
 * @property string $to_number
 * @property string $disconnect_reason
 * @property string $entry_id
 */
abstract Class  MangoOfficeFieldData {

    /**
     * Доступные поля
     *
     * @var array
     */
    protected $append_fields = [];

    /**
     * Поля, возможные для использования в статистике и нужные форматы
     *
     * @var array
     */
    protected $available_fields = [
        'records'           => 'array',
        'start'             => 'timestamp',
        'finish'            => 'timestamp',
        'from_extension'    => 'string',
        'from_number'       => 'string',
        'to_extension'      => 'string',
        'to_number'         => 'string',
        'disconnect_reason' => 'string',
        'entry_id'          => 'string',
        'sip'               => 'string'
    ];

    /**
     * Для получения нужной переменной
     *
     * @param $name
     *
     * @return mixed
     * @throws \Exception
     */
    function __get($name) {
        if (isset($this->append_fields[$name])) {
            return $this->append_fields[$name];
        }
        throw new \Exception('Undefined param [' . $name . ']');
    }

    /**
     * Инициализация класса, передать массив с данными
     *
     * @param array $data
     */
    function __construct(array $data) {
        foreach ($data as $k => $v) {
            $this->set($k, $v);
        }
    }

    function toArray() {
        $res = [];
        foreach ($this->append_fields as $k=>$field) {
            $res[$k] = (array)$field;
        }
        return $res;
    }

    abstract public function set($k, $v);
}