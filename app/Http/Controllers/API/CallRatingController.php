<?php

namespace App\Http\Controllers\API;

use App\CallVote;
use App\CallVotesLinkCallRating;
use App\Exceptions\APIException;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Validator;
use Illuminate\Support\Facades\DB;
use App\CallRating;

class CallRatingController extends APIController
{
    public function index()
    {
        return response()->json(['success' => CallRating::all()], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function show($id)
    {
        return response()->json(['success' => CallRating::find($id)], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function store(Request $request)
    {
        return response()->json(['success' => CallRating::create($request->all())], 201, [], JSON_NUMERIC_CHECK);
    }

    public function update(Request $request, $id)
    {
        $callRatings = CallRating::findOrFail($id);
        return response()->json(['success' => $callRatings->update($request->all())], 200, [], JSON_NUMERIC_CHECK);
    }

    public function delete(Request $request, $id)
    {
        $callRatings = CallRating::findOrFail($id);
        return response()->json(['success' => $callRatings->delete()], 204, [], JSON_NUMERIC_CHECK);
    }

    public function setRating(Request $request)
    {

        $callVote = CallVote::where('call_entry_id', $request->get('call_entry_id'))->first();

        if(!$callVote) {
            $callVote = new callVote;
        }
        $callVote->call_entry_id = $request->get('call_entry_id');
        $callVote->comment = $request->get('comment');
        $callVote->save();
        $requestRating = $request->get('rating', '');

        $requestRating = json_decode($requestRating, true);

        if (!$requestRating || !is_array($requestRating)) {
            return response()->json(['success' => false], 200, [], JSON_NUMERIC_CHECK);
        }

        foreach ($requestRating as $rating) {
            if (!CallRating::saveRating($callVote->id, $rating['value'], $rating['ratingID'], $rating['managerID'])) {
                return response()->json(['success' => false], 200, [], JSON_NUMERIC_CHECK);
            }
        }
        return response()->json(['success' => true], 200, [], JSON_NUMERIC_CHECK);
    }

}