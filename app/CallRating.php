<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallRating extends Model
{
    protected $fillable = ['name', 'code'];

    /**
     * @param $entryID
     * @return array
     */
    public static function getRatingsByCallEntryID($entryID)
    {
        $rating = self::all()->toArray();
        $rating = array_map(function ($item) {
            $item['value'] = 0;
            return $item;
        }, $rating);
        $callVote = CallVote::where('call_entry_id', $entryID)->first();
        if (!empty($callVote)) {
            $callVoteLinkRatings = CallVotesLinkCallRating::where('call_vote_id', $callVote->id)
                ->get()
                ->toArray();
            $callVoteLinkRatingArray = [];
            foreach ($callVoteLinkRatings as $item) {
                $callVoteLinkRatingArray[$item['rating_id']] = $item;
            }
            $rating = array_map(function ($item) use ($callVoteLinkRatingArray) {
                $item['value'] =  isset($callVoteLinkRatingArray[$item['id']])
                    ?$callVoteLinkRatingArray[$item['id']]['value']
                    :0;
                return $item;
            }, $rating);

        }

        return [
            'rating' => $rating,
            'ratingComment' => $callVote?$callVote->comment:null
        ];
    }

    /**
     * @param $voteID
     * @param $value
     * @param $ratingID
     * @param $managerID
     * @return bool
     */
    public static function saveRating($voteID, $value, $ratingID, $managerID)
    {
        $callVoteLinkRating = CallVotesLinkCallRating::where('call_vote_id', $voteID)
            ->where('rating_id', $ratingID)
            ->first();
        if(!$callVoteLinkRating) {
            $callVoteLinkRating = new CallVotesLinkCallRating;
        }
        $callVoteLinkRating->call_vote_id = $voteID;
        $callVoteLinkRating->value = $value;
        $callVoteLinkRating->rating_id = $ratingID;
        $callVoteLinkRating->manager_id = $managerID;
        return $callVoteLinkRating->save();
    }
}
