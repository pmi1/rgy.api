<?php
namespace App\GraphQL\Query;

use App\LegalEntity;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;

class LegalEntityQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'LegalEntity'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('LegalEntity'))));
    }

    public function resolve($root, $args)
    {
        return LegalEntity::all();
    }
}