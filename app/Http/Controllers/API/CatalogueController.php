<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Catalogue;
use App\Item;
use App\Role;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\APIException;
use Carbon\Carbon;

class CatalogueController extends APIController
{

    public function catalogue(Request $request)
    {
        /**
         * @var \App\User
         */
        $user = Auth::user();
        if (! $user->hasSupermanager() && !$user->hasPid() && !$user->hasManager() && !$user->hasAnyRole(array_merge(['cto', 'equipment'], Role::WAREHOUSE_CODES))) {
            throw new APIException('У вас нет прав для доступа к этой странице');
        }

        $catalogueObject = new Catalogue();
        $query = $catalogueObject->getList(array_merge($request->all(), ['pid' => $user->stand_id]));

        return response()->json($query->get(), 200, [], JSON_NUMERIC_CHECK);

    }


    public function items(Request $request)
    {
        /**
         * @var \App\User
         */
        $user = Auth::user();
        if (! $user->hasAnyRole(['supermanager', 'stand'])) {
            throw new APIException('У вас нет прав для доступа к этой стринице');
        }

        $itemObject = new Item();
        $query = $itemObject->getList(array_merge($request->all(), ['pid' => $user->stand_id]))
                    ->where('t.realstatus', 1);

        return response()->json(['success' => ['data' => $itemObject->prepare($query->get())]], $this->successStatus, [], JSON_NUMERIC_CHECK);

    }
}