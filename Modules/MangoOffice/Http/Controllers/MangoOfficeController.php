<?php

namespace Modules\MangoOffice\Http\Controllers;

use App\CallLinkCallType;
use App\CallRating;
use App\CallType;
use App\CallVote;
use App\Helpers\Helper;
use App\Http\Controllers\API\CallRatingController;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\MangoOffice\Library\MangoHelper;
use Illuminate\Pagination\LengthAwarePaginator;
class MangoOfficeController extends Controller
{
    public function getStats(Request $request) {
        $data = [];
        $fromNumber = $request->get('from_number');
        $toNumber = $request->get('to_number');
        if ($managersID = $request->get('managersID')) {
            $managersID = json_decode($managersID);
                $managers = DB::table('user')->whereIn('user_id', $managersID)
                    ->get(['mango_user_id']);

            foreach ($managers as $manager) {
                if (empty($manager->mango_user_id)) {
                    continue;
                }
                $data += MangoHelper::getStat(
                    $request->get('dateFrom'),
                    $request->get('dateTo'),
                    $request->get('from'),
                    $fromNumber,
                    $request->get('to'),
                    $toNumber,
                    $request->get('fields'),
                    $request->get('requestId'),
                    $manager->mango_user_id
                );

                $data += MangoHelper::getStat(
                    $request->get('dateFrom'),
                    $request->get('dateTo'),
                    $request->get('from'),
                    $fromNumber,
                    $request->get('to'),
                    $toNumber,
                    $request->get('fields'),
                    $request->get('requestId'),
                    null,
                    $manager->mango_user_id
                );
            }


        }
        else {
            $data += MangoHelper::getStat(
                $request->get('dateFrom'),
                $request->get('dateTo'),
                $request->get('from'),
                $fromNumber,
                $request->get('to'),
                $request->get('to_number'),
                $request->get('fields'),
                $request->get('requestId')
            );
        }

        $result = [];
        $isRecord = $request->get('isRecord');
        if (is_array($data)) {
            $mangoPhones = [];
            foreach ($data as $item) {
                if (isset($item['from_number'][0])) {
                    $mangoPhones[] = Helper::clearOnlyLettersFromString($item['from_number'][0]);
                }
                if (isset($item['to_number'][0])) {
                    $mangoPhones[] = Helper::clearOnlyLettersFromString($item['to_number'][0]);
                }
            }
            $mangoPhones = array_unique($mangoPhones);

            $userResult = DB::table('user')
                ->whereIn("phone", $mangoPhones)
                ->get([
                    'user_id as id',
                    'email',
                    'name',
                    'phone',
                    'lastname',
                    'secondname'
                ])->toArray();

            $users = [];
            foreach ($userResult as $item) {
                $users[$item->phone] = $item;
            }
            $managersResult  = DB::table('user')
                ->where('mango_user_id', '>', 0)
                ->get([
                    'user_id as id',
                    'email',
                    'name',
                    'lastname',
                    'secondname',
                    'phone',
                    'mango_user_id'
                ])
                ->toArray();
            $managers = [];
            foreach ($managersResult as $item) {
                $managers[$item->mango_user_id] = $item;
            }
        }
        $sortDir = $request->get('sortDir', 'desc');

        usort($data, function ($item1, $item2) use ($sortDir) {
            $dateTime1 = Carbon::createFromTimeString($item1['start']['date'], 'Europe/Moscow');
            $dateTime2= Carbon::createFromTimeString($item2['start']['date'], 'Europe/Moscow');
            $multipier = $sortDir=='asc'?1:-1;
            $diff = $multipier * $dateTime1->diffInSeconds($dateTime2, false);
            return $diff<0?1:-1;
        });

        $data  = array_map(function($item) use ($users, $managers){
            if(!empty($item['to_extension'][0]) || strpos($item['to_number'][0], 'sipuser')) {
                $mango_user_id = $item['to_extension'][0];
                $number = Helper::clearOnlyLettersFromString($item['from_number'][0]);
                $customer = isset($users[$number])?(array)$users[$number]:null;
                $outgoing = false;
                $manager = isset($managers[$mango_user_id])?$managers[$mango_user_id]:null;
                if ($manager == null) {
                    $manager = [
                        'phone' => $item['to_number'][0],
                        'mango_user_id' => $item['to_extension'][0]
                    ];
                }

                if ($customer == null) {
                    $customer = [
                        'phone' => $number
                    ];
                }
            }
            else {
                $mango_user_id = $item['from_extension'][0];
                $number = Helper::clearOnlyLettersFromString($item['to_number'][0]);
                $customer = isset($users[$number])?(array)$users[$number]:null;
                $outgoing = true;
                $manager = isset($managers[$mango_user_id])?$managers[$mango_user_id]:null;
                if ($manager == null) {
                    $manager = [
                        'phone' => $item['from_number'][0],
                        'mango_user_id' => $item['from_extension'][0]
                    ];
                }

                if ($customer == null) {
                    $customer = [
                        'phone' => $number
                    ];
                }
            }

            $item['outgoing'] = $outgoing;
            $item['manager'] = $manager;
            $item['customer'] = $customer;
            return $item;
        }, $data );

        if ($clientID = $request->get('clientID')) {
            $clientPhone = DB::table('user')
                ->where('user_id', $clientID)
                ->get(['phone'])
                ->first()->phone;
            $data = array_filter($data, function($item) use($clientPhone, $isRecord)
            {
                return $item['customer']['phone'] == $clientPhone;
            });
        }

        if ($query = $request->get('query')) {
            $data = array_filter($data, function($item) use($query)
            {
                if (strpos($item['from_number'][0], $query)!==false || strpos($item['to_number'][0], $query)!==false) {
                    return true;
                }

                $customerName = isset($item['customer']['name'])?mb_strtolower($item['customer']['name']):null;
                if ($customerName && strpos($customerName, mb_strtolower($query))!==false) {
                    return true;
                }

                $customerLastName = isset($item['customer']['lastname'])?mb_strtolower($item['customer']['lastname']):null;
                if ($customerLastName && strpos($customerLastName, mb_strtolower($query))!==false) {
                    return true;
                }

                return false;
            });
        }

        $data = array_filter($data, function ($item) use ($isRecord) {
            if ($isRecord && empty($item['records'])) {
                return false;
            }
            return true;
        });

        $countPerPage = env('PAGINATION_COUNT_PER_PAGE');

        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a new Laravel collection from the array data
        $itemCollection = collect($data);

        // Define how many items we want to be visible in each page
        $perPage = $countPerPage;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();

        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        $items = array_values($paginatedItems->items());
        // set url path for generted links
        $paginatedItems->setPath($request->url());

        foreach ($items as $item) {
            $startTime = Carbon::createFromTimeString($item['start']['date'], 'Europe/Moscow');
            $endTime = Carbon::createFromTimeString($item['finish']['date'], 'Europe/Moscow');
            $totalDuration = $endTime->diffInSeconds($startTime);
            $entryID = $item['entry_id'][0];
            $callRatings = CallRating::getRatingsByCallEntryID($entryID);
            $callType = CallLinkCallType::where('call_entry_id', '=', $entryID)->first();
            $result[] = [
                'entry_id' => $entryID,
                'outgoing' => $item['outgoing'],
                'from_number' => Helper::clearOnlyLettersFromString($item['from_number'][0]),
                'to_number' => Helper::clearOnlyLettersFromString($item['to_number'][0]),
                'records' => $item['records'],
                'date' => $item['start']['date'],
                'duration' => $totalDuration,
                'manager' => $item['manager'],
                'customer' => $item['customer'],
                'callType' => $callType
                                  && ($callTypeItem = $callType->callType()->first())
                                  ?$callTypeItem->id
                                  :null,
                'callState' => null,
                'rating' => $callRatings['rating'],
                'ratingComment' => $callRatings['ratingComment'],
            ];
        }

        return response()->json(
            [
                'success' => [
                    'data' => $result,
                    'current_page' => $paginatedItems->currentPage(),
                    'last_page' => $paginatedItems->lastPage(),
                    'total' => $paginatedItems->total(),
                ]
            ]);
    }

    public function getRecord(Request $request)
    {
        $recordID = $request->get('recordID');
        $recordPath = MangoHelper::getRecord($recordID);

        return response()->json(
            [
                'success' => [
                    'data' => $recordPath
                ]
            ]
        );
    }
}
