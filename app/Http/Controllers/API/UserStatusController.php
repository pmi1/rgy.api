<?php

namespace App\Http\Controllers\API;

use App\CallLinkUserStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Validator;
use Illuminate\Support\Facades\DB;
use App\UserStatus;

class UserStatusController extends APIController
{
    public function index()
    {
        return response()->json(['success' => UserStatus::all(UserStatus::$fieldsResponse)], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function show($id)
    {
        return response()->json([
            'success' => UserStatus::where('status_id', $id)
            ->get(UserStatus::$fieldsResponse)
            ->first()
        ],
            $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function store(Request $request)
    {
        return response()->json(['success' => UserStatus::create($request->all())], 201, [], JSON_NUMERIC_CHECK);
    }

    public function update(Request $request, $id)
    {
        $callType = UserStatus::findOrFail($id);
        return response()->json(['success' => $callType->update($request->all())], 200, [], JSON_NUMERIC_CHECK);
    }

    public function delete(Request $request, $id)
    {
        $callType = UserStatus::findOrFail($id);
        return response()->json(['success' => $callType->delete()], 204, [], JSON_NUMERIC_CHECK);
    }
}