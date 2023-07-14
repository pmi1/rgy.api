<?php
namespace App\GraphQL\Type;

use App\Contractor;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ContractorType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Contractor',
        'description' => 'A link between product and order',
        'model' => Contractor::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of contractor',
                'alias' => 'contractor_id',
            ],
            'cmfSite' => [
                'type' => GraphQL::type('CmfSite'),
                'description' => 'The cmfSite of order',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name',
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'The email',
                'alias' => 'emails',
            ],
            'phone' => [
                'type' => Type::string(),
                'description' => 'The phone',
            ],
            'site' => [
                'type' => Type::string(),
                'description' => 'The site',
            ],
            'image' => [
                'type' => Type::string(),
                'description' => 'The image',
            ],
            'discount' => [
                'type' => Type::string(),
                'description' => 'The discount',
                'alias' => 'max_discount_percent_for_contractor',
            ],
            'address' => [
                'type' => Type::string(),
                'description' => 'The address',
            ],
            'warehouseAddress' => [
                'type' => Type::string(),
                'description' => 'The warehouseAddress',
                'alias' => 'address_sklad',
            ],
            'products' => [
                'type' => Type::listOf(GraphQL::type('ProductContractor')),
                'description' => 'The product',
                'alias' => 'items',
            ],
        ];
    }
}