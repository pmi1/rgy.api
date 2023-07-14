<?php
namespace App\GraphQL\Type;

use App\UserCategory;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserCategoryType extends GraphQLType
{
    protected $attributes = [
        'name' => 'UserCategory',
        'description' => 'A User Category',
        'model' => UserCategory::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the user category',
                'alias' => 'user_category_id',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The email of user category'
            ],
            'discount' => [
                'type' => Type::string(),
                'description' => 'The discount of user category'
            ],
            'cashback' => [
                'type' => Type::string(),
                'description' => 'The cashback of user category',
            ],
            'limit' => [
                'type' => Type::string(),
                'description' => 'The limit of user category',
            ],
            'comment' => [
                'type' => Type::string(),
                'description' => 'The comment of user category',
            ],
            'paymentCashback' => [
                'type' => Type::int(),
                'description' => 'The payment cashback of user category',
                'alias' => 'payment_cashback',
            ],
            'discountPromocode' => [
                'type' => Type::string(),
                'description' => 'The discount promocode',
                'alias' => 'discount_promocode',
            ],
            'cashbackPromocode' => [
                'type' => Type::string(),
                'description' => 'The cashback promocode',
                'alias' => 'cashback_promocode',
            ],
        ];
    }
}