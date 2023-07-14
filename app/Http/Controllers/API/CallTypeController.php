<?php

namespace App\Http\Controllers\API;

use App\CallLinkCallType;
use App\CallRating;
use App\Exceptions\APIException;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Validator;
use Illuminate\Support\Facades\DB;
use App\CallType;

class CallTypeController extends APIController
{
    public function index()
    {
        return response()->json(['success' => CallType::all()], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function show($id)
    {
        return response()->json(['success' => CallType::find($id)], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function store(Request $request)
    {
        return response()->json(['success' => CallType::create($request->all())], 201, [], JSON_NUMERIC_CHECK);
    }

    public function update(Request $request, $id)
    {
        $callType = CallType::findOrFail($id);
        return response()->json(['success' => $callType->update($request->all())], 200, [], JSON_NUMERIC_CHECK);
    }

    public function delete(Request $request, $id)
    {
        $callType = CallType::findOrFail($id);
        return response()->json(['success' => $callType->delete()], 204, [], JSON_NUMERIC_CHECK);
    }

    public function setCallType(Request $request)
    {
        $callLinkCallType = CallLinkCallType::where('call_entry_id', $request->get('call_entry_id'))->first();
        if ($callLinkCallType) {
            $callLinkCallType->update($request->all());
        }
        else {
            CallLinkCallType::create($request->all());
        }

        return response()->json(['success' => true], 200, [], JSON_NUMERIC_CHECK);
    }

}