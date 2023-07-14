<?php

namespace App\Exceptions;

use Exception;

class APIException extends Exception
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json(['extension' => $this->getMessage()]);
    }
}
