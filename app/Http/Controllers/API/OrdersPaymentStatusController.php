<?php

namespace App\Http\Controllers\API;

use App\CallLinkOrdersPaymentStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use App\OrdersPaymentStatus;

class OrdersPaymentStatusController extends APIController
{
    public function index()
    {
        return response()->json(['success' => OrdersPaymentStatus::all()], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function show($id)
    {
        return response()->json(['success' => OrdersPaymentStatus::find($id)], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function store(Request $request)
    {
        return response()->json(['success' => OrdersPaymentStatus::create($request->all())], 201, [], JSON_NUMERIC_CHECK);
    }

    public function update(Request $request, $id)
    {
        $callType = OrdersPaymentStatus::findOrFail($id);
        return response()->json(['success' => $callType->update($request->all())], 200, [], JSON_NUMERIC_CHECK);
    }

    public function delete(Request $request, $id)
    {
        $callType = OrdersPaymentStatus::findOrFail($id);
        return response()->json(['success' => $callType->delete()], 204, [], JSON_NUMERIC_CHECK);
    }
}