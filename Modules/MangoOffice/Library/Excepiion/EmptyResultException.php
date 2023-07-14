<?php
/**
 * Created by PhpStorm.
 * User: 
 * Date: 01/03/2019
 * Time: 00:33
 */

namespace Modules\MangoOffice\Library\Excepiion;


class EmptyResultException extends ApiException
{
    public function __construct()
    {
        $message = 'Empty result';
        parent::__construct($message, $code = 400);
    }
}