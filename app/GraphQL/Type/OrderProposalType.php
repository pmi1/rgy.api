<?php
namespace App\GraphQL\Type;

use App\OrderItemContractor;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderProposalType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderProposal',
        'description' => 'A  proposal for order',
        'model' => OrderItemContractor::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::id(),
                'description' => 'The id of order item contractor',
                'alias' => 'orders_item_contractor_id',
            ],
            'forId' => [
                'type' => Type::string(),
                'description' => 'The forId',
                'selectable' => false,
            ],
            'amount' => [
                'type' => Type::string(),
                'description' => 'The amount',
                'alias' => 'quantity',
            ],
            'price' => [
                'type' => Type::string(),
                'description' => 'The quantity',
            ],
            'checked' => [
                'type' => Type::boolean(),
                'description' => 'The checked',
            ],
            'visible' => [
                'type' => Type::string(),
                'description' => 'The visible',
            ],
            'pledge' => [
                'type' => Type::string(),
                'description' => 'The pledge',
                'alias' => 'deposit'
            ],
            'install' => [
                'type' => Type::string(),
                'description' => 'The install',
            ],
            'uninstall' => [
                'type' => Type::string(),
                'description' => 'The uninstall',
            ],
            'installPrice' => [
                'type' => Type::string(),
                'description' => 'The install Price',
                'alias' => 'price_montag',
            ],
            'dileveryIn' => [
                'type' => Type::string(),
                'description' => 'The dilevery in',
                'alias' => 'delivery',
            ],
            'deliveryOut' => [
                'type' => Type::string(),
                'description' => 'The delivery out',
                'alias' => 'pickup',
            ],
            'deliveryPrice' => [
                'type' => Type::string(),
                'description' => 'The delivery price',
                'alias' => 'price_delivery',
            ],
            'product' => [
                'type' => GraphQL::type('Product'),
                'description' => 'The product',
            ],
            'contractor' => [
                'type' => GraphQL::type('Contractor'),
                'description' => 'The contractor',
            ],
        ];
    }
}