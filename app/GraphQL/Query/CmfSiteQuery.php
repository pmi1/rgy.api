<?php
namespace App\GraphQL\Query;

use App\CmfSite;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;

class CmfSiteQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'CmfSite'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('CmfSite'))));
    }

    public function resolve($root, $args)
    {
       return CmfSite::all();
    }
}