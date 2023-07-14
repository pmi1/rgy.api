<?php

namespace App\Http\Controllers\API;

use App\CallLinkTodoManager;
use App\CallRating;
use App\Exceptions\APIException;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Tests\Unit\TodoManagerTest;
use Validator;
use Illuminate\Support\Facades\DB;
use App\TodoManager;

class TodoManagerController extends APIController
{
    public function index(Request $request)
    {
        return response()->json(['success' => TodoManager::getList($request->all())], $this->successStatus);
    }

    public function show($id)
    {
        return response()->json(['success' => TodoManager::getById($id)], $this->successStatus);
    }

    public function store(Request $request)
    {
        $params = $request->all();
        if (isset($params['date'])) {
            $params['date'] = str_replace('-', '.', $params['date']);
        }

        return response()->json(['success' => TodoManager::create($params)], 201);
    }

    public function update(Request $request, $id)
    {
        $todoManager = TodoManager::findOrFail($id);
        return response()->json(['success' => $todoManager->save($request->all())], 200);
    }

    public function delete(Request $request, $id)
    {
        $callType = TodoManager::findOrFail($id);
        return response()->json(['success' => $callType->delete()], 204);
    }

    public function search(Request $request)
    {
        $searchQuery = TodoManager::search($request->all());

        $perPage = $searchQuery->count();
        if ( $request->get('enablePaginator') ) {
            $perPage = env('PAGINATION_COUNT_PER_PAGE');
        }
        $search = $searchQuery->paginate($perPage);
        $items = $search
            ->getCollection()->transform(function ($item){
        return [
            'id' => $item->id,
            'typeId'  => $item->typeId,
            'statusId'  => $item->statusId,
            'stateId'  => $item->stateId,
            'priorityId'  => $item->priorityId,
            'responsible'  => $item
                ->responsible()
                ->first([
                    'user_id as id',
                    'name',
                    'lastname as lastName',
                    'secondname as secondName',
                    'phone',
                    'email',
                ]),
            'date'  => $item->date,
            'authorId'  => $item->authorId,
            'createdAt'  => $item->createdAt,
            'orderId'  => $item->orderId,
            'comment'  => $item->comment
        ];
    });
        return response()->json(
            [
                'success' => [
                    'data' => $items,
                    'current_page' => $search->currentPage(),
                    'last_page' => $search->lastPage(),
                    'total' => $search->total()
                ]
            ],
            $this->successStatus);
    }
}