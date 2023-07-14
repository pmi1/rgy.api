<?php

namespace App\Http\Controllers\API;

use App\Exceptions\APIException;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use App\User;
use App\Order;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class CarController extends APIController
{
    public function list()
    {
        $cars = DB::table('car')
            ->get();
        return response()->json(['success' => $cars], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }
}