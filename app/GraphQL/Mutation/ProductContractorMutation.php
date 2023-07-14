<?php

namespace App\GraphQL\Mutation;

use CLosure;
use App\User;
use App\Role;
use App\Item;
use App\Contractor;
use App\Exceptions\APIException;
use App\ItemContractor;
use App\CmfSequence;
use GraphQL;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use App\GraphQL\RguysMutation;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\HasMiddleware;
use Carbon\Carbon;

class ProductContractorMutation extends RguysMutation
{
    protected $attributes = [
        'name' => 'ProductContractor'
    ];

    public function type(): Type
    {
        return GraphQL::type('ProductContractor');
    }

    public function args(): array
    {
        return [
            'product' => ['name' => 'product', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'contractor' => ['name' => 'contractor', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'price' => ['name' => 'price', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'quantity' => ['name' => 'quantity', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $byUser = Auth::user();

        if (!$byUser->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES))) {

            throw new APIException('У вас нет прав для доступа к этой странице');
        }

        $item = Item::where('cmf_site_id', config('common.siteId', 1))
            ->where('item_id', $args['product'])->first();

        if(!$item) {

            throw new APIException('Product not found!');
        }


        $contractor = Contractor::where('cmf_site_id', config('common.siteId', 1))
            ->where('contractor_id', $args['contractor'])->first();

        if(!$contractor) {

            throw new APIException('Contractor not found!');
        }

        if (!($p = ItemContractor::where('cmf_site_id', config('common.siteId', 1))
            ->where('item_id', $args['product'])->where('contractor_id', $args['contractor'])->first())) {

            $p = new ItemContractor();
            $p->cmf_site_id = config('common.siteId', 1);
            $p->id = ItemContractor::where('cmf_site_id', config('common.siteId', 1))->max('id')+1;

            if ($s = CmfSequence::where('name', 'item_contractor')->first()) {

                $s->id = $p->id;
                $s->save();
            }
        }

        $fields = ['product' => 'item_id', 'contractor' => 'contractor_id', 'quantity' => 'available_quantity'
            , 'price' => 'price'];

        foreach ($fields as $key => $value) {

            if (isset($args[$key])) {

                $p[$value] = $args[$key];
            }
        }

        $p->save();

        return $p;
    }
}