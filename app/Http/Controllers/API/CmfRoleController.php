<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\APIException;
use App\CmfRole;

class CmfRoleController extends APIController
{

    public function list() {
        /**
         * @var \App\User
         */
        $user = Auth::user();

        $roles = CmfRole::all()
            ->map(function ($item){
                return [
                    'id' => $item->cmf_role_id,
                    'name' => $item->name,
                    'urlRedirect' => $item->cmf_url,
                    'code' => $item->code,

                ];
            });

        return response()->json(['success' => $roles, $this->successStatus, [], JSON_NUMERIC_CHECK]);
    }
}