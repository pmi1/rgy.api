<?php
namespace App\GraphQL\Type;

use App\ItemContractor;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProductContractorType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ProductContractor',
        'description' => 'A link between product and contractor',
        'model' => ItemContractor::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::string(),
                'alias' => 'id'
            ],
            'quantity' => [
                'type' => Type::string(),
                'description' => 'The quantity of product',
                'alias' => 'available_quantity'
            ],
            'price' => [
                'type' => Type::string(),
                'description' => 'The price of product',
            ],
            'contractor' => [
                'type' => GraphQL::type('Contractor'),
                'description' => 'The Contractor',
            ],
            'product' => [
                'type' => GraphQL::type('Product'),
                'description' => 'The product',
            ],
        ];
    }
}