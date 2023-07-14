<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use App\UserStyle;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;

class PlatformController extends APIController
{
    public function index() {
        $platforms = DB::table('user')
            ->whereNotNull('stand_id')
            ->get(['stand_id', 'theme', 'fonts']);

        return response()->json(['success' => $platforms], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }
}
