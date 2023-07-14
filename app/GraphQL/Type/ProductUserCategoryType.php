<?php
namespace App\GraphQL\Type;

use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use App\ItemUserCategory;

class ProductUserCategoryType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ProductUserCategory',
        'description' => 'A call type',
        'model' => ItemUserCategory::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id',
                'alias' => 'item_user_category_id',
            ],
            'discount' => [
                'type' => Type::string(),
                'description' => 'The discount',
            ],
            'userCategory' => [
                'type' => GraphQL::type('UserCategory'),
                'description' => 'The user categiry',
            ],
        ];
    }
}