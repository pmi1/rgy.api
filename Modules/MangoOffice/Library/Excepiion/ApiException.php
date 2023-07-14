<?php

namespace Modules\MangoOffice\Library\Excepiion;

class ApiException extends \Exception
{
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        echo json_encode(array("success" => false, "message" => $message));
        exit;
    }
}