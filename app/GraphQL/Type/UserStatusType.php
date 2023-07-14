<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserStatusType extends GraphQLType
{
    protected $attributes = [
        'name' => 'UserStatus',
        'description' => 'An user status'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the user status'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the user status'
            ],
            'statusDiscount' => [
                'type' => Type::int(),
                'description' => 'The percent of discount of the user status'
            ],
            'statusPercentBonuses' => [
                'type' => Type::string(),
                'description' => 'The description of the user status'
            ],
            'cashflowFrom' => [
                'type' => Type::int(),
                'description' => 'The start cash flow of the user status'
            ],
            'cashflowTo' => [
                'type' => Type::int(),
                'description' => 'The end cash flow of the user status'
            ]
        ];
    }

    protected function resolveIdField($root, $args)
    {
        return $root->status_id;
    }

    protected function resolveStatusDiscountField($root, $args)
    {
        return $root->status_discount;
    }

    protected function resolveStatusPercentBonusesField($root, $args)
    {
        return $root->status_percent_bonuses;
    }

    protected function resolveCashflowFromField($root, $args)
    {
        return $root->oborot_from;
    }

    protected function resolveCashflowToField($root, $args)
    {
        return $root->oborot_to;
    }
}