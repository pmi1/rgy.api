<?php

namespace App\Http\Controllers\API;

use App\CallLinkDiscounter;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Discounter;

class DiscounterController extends APIController
{
    public function index()
    {
        return response()->json(['success' => Discounter::all(Discounter::$returnedFields)], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function show($id)
    {
        return response()->json(['success' =>
            Discounter::where('discounter_id', $id)
                ->get(Discounter::$returnedFields)
                ->first()
        ],
            $this->successStatus);
    }

    public function store(Request $request)
    {
        return response()->json(['success' => Discounter::create($request->all())], 201, [], JSON_NUMERIC_CHECK);
    }

    public function update(Request $request, $id)
    {
        $callType = Discounter::findOrFail($id);
        return response()->json(['success' => $callType->update($request->all())], 200, [], JSON_NUMERIC_CHECK);
    }

    public function delete(Request $request, $id)
    {
        $callType = Discounter::findOrFail($id);
        return response()->json(['success' => $callType->delete()], 204, [], JSON_NUMERIC_CHECK);
    }
}