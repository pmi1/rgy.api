<?php

namespace App\Http\Controllers\API;

use App\CallLinkContractor;
use App\CallRating;
use App\Exceptions\APIException;
use App\SearchFilters\ContractorSearch\ContractorSearch;
use App\SearchFilters\ContractorSearch\ContractorSearchResult;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Predis\Command\ConnectionAuth;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Contractor;
use Illuminate\Support\Facades\Auth;

class ContractorController extends APIController
{
    public function index()
    {
        $contractors = Contractor::all()->where('cmf_site_id', config('common.siteId', 1))
        ->map(function($item) {
            return [
               'id' => $item->contractor_id,
               'name' => $item->name,
               'image' => $item->image,
               'discountPercentForRguys' => $item->discount_percent_for_rguys,
               'phone' => $item->phone,
               'site' => $item->site,
               'userName' => $item->user_name,
               'userLastName' => $item->user_lastname,
               'warehouseAddress' => $item->address_sklad,
               'emails' => $item->emails,
               'maxDiscountPercentForContractor' => $item->max_discount_percent_for_contractor
            ];
        });

        return response()->json(['success' => $contractors], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function show($id)
    {
        $contractor = Contractor::where('cmf_site_id', config('common.siteId', 1))->where('contractor_id', $id)->first();
        $contractorPrepare = [
            'id' => $contractor->contractor_id,
            'name' => $contractor->name,
            'logo' => $contractor->image,
            'discount' => $contractor->discount_percent_for_rguys,
            'phone' => $contractor->phone,
            'site' => $contractor->site,
            'userName' => $contractor->user_name,
            'userLastName' => $contractor->user_lastname,
            'warehouseAddress' => $contractor->address_sklad,
            'emails' => $contractor->emails,
            'maxDiscountPercentForContractor' => $contractor->max_discount_percent_for_contractor
        ];
        return response()->json(['success' => $contractorPrepare], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function store(Request $request)
    {
        $fieldsMap = [
            'name' => 'name',
            'max_discount_percent_for_contractor' => 'maxDiscountPercentForContractor',
            'discount_percent_for_rguys' => 'discountPercentForRguys',
            'description' => 'description',
            'image' => 'image',
            'address' => 'address',
            'address_sklad' => 'storeAddress',
            'site' => 'site',
            'city' => 'city',
            'phone' => 'phone',
            'emails' => 'emails',
            'user_name' => 'userName',
            'user_lastname' => 'userLastName',
            'show_logo' => 'showLogo',
            'force_delivery' => 'forceDelivery',
            'status' => 'status',
            'city_id' => 'cityID'
        ];
        $preparedFields = ['cmf_site_id' => config('common.siteId', 1)];
        foreach ($fieldsMap as $key => $value) {
            if ($request->has($value)) {
                $preparedFields[$key] = $request->get($value);
            }

        }
        return response()->json(['success' => Contractor::create($preparedFields)], 201, [], JSON_NUMERIC_CHECK);
    }

    public function update(Request $request, $id)
    {
        $callType = Contractor::where('cmf_site_id', config('common.siteId', 1))->where('contractor_id', $id)->first();
        $fieldsMap = [
            'name' => 'name',
            'max_discount_percent_for_contractor' => 'maxDiscountPercentForContractor',
            'discount_percent_for_rguys' => 'discountPercentForRguys',
            'description' => 'description',
            'image' => 'image',
            'address' => 'address',
            'address_sklad' => 'storeAddress',
            'site' => 'site',
            'city' => 'city',
            'phone' => 'phone',
            'emails' => 'emails',
            'user_name' => 'userName',
            'user_lastname' => 'userLastName',
            'show_logo' => 'showLogo',
            'force_delivery' => 'forceDelivery',
            'status' => 'status',
            'city_id' => 'cityID'
        ];
        $preparedFields = [];
        foreach ($fieldsMap as $key => $value) {
            if ($request->has($value)) {
                $preparedFields[$key] = $request->get($value);
            }

        }
        return response()->json(['success' => $callType->update($preparedFields)], 200, [], JSON_NUMERIC_CHECK);
    }

    public function delete(Request $request, $id)
    {
        $callType = Contractor::where('cmf_site_id', config('common.siteId', 1))->where('contractor_id', $id)->first();
        return response()->json(['success' => $callType->delete()], 204, [], JSON_NUMERIC_CHECK);
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasSupermanager() || !$user->hasManager()) {
            throw new APIException('У вас нет прав для доступа к этой стринице');
        }

        $queryBuilder = (new ContractorSearchResult(ContractorSearch::apply($request->all())));

        $queryBuilder
            ->getBuilder()
            ->with(['items' => function($query) use ($request) {
            $query->select(
                [
                    'id',
                    'contractor_id',
                    'item_id',
                    'price',
                    'min_quantity as minQuantity',
                    'available_quantity as quantity',
                    'status',
                    'deposit',
                    'bonus',
                    'cashback'
                ]
            );
            $filterItemByItemID = $request->get('filterItemByItemID', false);
            if ($filterItemByItemID && $itemIDs = $request->get('itemIDs', false)) {
                if (is_string($itemIDs)) {
                    $itemIDs = json_decode($itemIDs);
                }

                if (is_int($itemIDs)) {
                    $itemIDs = [$itemIDs];
                }
                $query->whereIn('item_id', $itemIDs);
            }
        }]);
        $result = $queryBuilder->get();
        $result->map(function ($item) {
            $item->id = $item->contractor_id;
            unset($item->contractor_id);
            return $item;
        });
        return response()->json(['success' => $result], 200, [], JSON_NUMERIC_CHECK);
    }
}