<?php

namespace App\Http\Controllers\API;

use App\CallLinkTodoPriority;
use App\CallRating;
use App\Exceptions\APIException;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Validator;
use Illuminate\Support\Facades\DB;
use App\TodoPriority;

class TodoPriorityController extends APIController
{
    public function index()
    {
        return response()->json(['success' => TodoPriority::all()], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function show($id)
    {
        return response()->json(['success' => TodoPriority::find($id)], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function store(Request $request)
    {
        return response()->json(['success' => TodoPriority::create($request->all())], 201, [], JSON_NUMERIC_CHECK);
    }

    public function update(Request $request, $id)
    {
        $callType = TodoPriority::findOrFail($id);
        return response()->json(['success' => $callType->update($request->all())], 200, [], JSON_NUMERIC_CHECK);
    }

    public function delete(Request $request, $id)
    {
        $callType = TodoPriority::findOrFail($id);
        return response()->json(['success' => $callType->delete()], 204, [], JSON_NUMERIC_CHECK);
    }
}