<?php
namespace App\Helpers;

use App\MaillistTemplate;
use Illuminate\Support\Facades\DB;

class Helper
{
    /**
     * Оставляет только цифры
     * @param $string
     */
    static public function clearOnlyLettersFromString($string) {
        $string =  preg_replace('/fmc\:/', '', $string);
        $string =  preg_replace('/[^0-9]/', '', $string);
        return $string;
    }

    /**
     * send email by template
     * @param $templateId
     * @param $params
     */
    static public function mail($templateId, $params = [])
    {

        $mail = new MaillistTemplate();
        $mail->sendMail($templateId, $params);

        return $mail;
    }

    #--------------------------------------------------------------------------------
    static public function addSmsLog($userId, $event, $requestUrl, $response)
    {
        DB::table('smslog')->insert(array(
            'user_id' => $userId,
            'date_insert' => date('Y-m-d H:i:s'),
            'event' => $event,
            'request_url' => $requestUrl,
            'response' => $response,
            'url' => (isset($_SERVER['HTTPS']) ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
            'description' => '',
        ));
    }

    static public function dsCrypt($input, $decrypt = false)
    {
        $encrypt_arr = array(
            'a' => 'c',
            'b' => 'd',
            'c' => 'e',
            'd' => 'f',
            'e' => 'g',
            'f' => 'h',
            'g' => 'i',
            'h' => 'j',
            'i' => 'k',
            'j' => 'l',
            'k' => 'm',
            'l' => 'n',
            'm' => 'o',
            'n' => 'p',
            'o' => 'q',
            'p' => 'r',
            'q' => 's',
            'r' => 't',
            's' => 'u',
            't' => 'v',
            'u' => 'w',
            'v' => 'x',
            'w' => 'y',
            'x' => 'z',
            'y' => 'a',
            'z' => 'b',
        );
        $decrypt_arr = array(
            'c' => 'a',
            'd' => 'b',
            'e' => 'c',
            'f' => 'd',
            'g' => 'e',
            'h' => 'f',
            'i' => 'g',
            'j' => 'h',
            'k' => 'i',
            'l' => 'j',
            'm' => 'k',
            'n' => 'l',
            'o' => 'm',
            'p' => 'n',
            'q' => 'o',
            'r' => 'p',
            's' => 'q',
            't' => 'r',
            'u' => 's',
            'v' => 't',
            'w' => 'u',
            'x' => 'v',
            'y' => 'w',
            'z' => 'x',
            'a' => 'y',
            'b' => 'z',
        );
        $output = '';
        $strlen = strlen($input);
        if ($decrypt) {
            for ($i = 0; $i < $strlen; $i++) {
                if (isset($decrypt_arr[$input[$i]])) {
                    $output .= $decrypt_arr[$input[$i]];
                } else {
                    $output .= $input[$i];
                }
            }
        } else {
            for ($i = 0; $i < $strlen; $i++) {
                if (isset($encrypt_arr[$input[$i]])) {
                    $output .= $encrypt_arr[$input[$i]];
                } else {
                    $output .= $input[$i];
                }
            }
        }

        return $output;
    }

}

?>