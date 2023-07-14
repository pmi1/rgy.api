<?php
namespace App\GraphQL\Type;

use App\OrderDocument;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class OrderDocumentType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderDocument',
        'description' => 'A order document',
        'model' => OrderDocument::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the order document',
            ],
            'order' => [
                'type' => GraphQL::type('Order'),
                'description' => 'The order of order document'
            ],
            'legalEntity' => [
                'type' => GraphQL::type('LegalEntity'),
                'description' => 'The legal entity of order document'
            ],
            'number' => [
                'type' => Type::string(),
                'description' => 'The name of the order document'
            ],
            'description' => [
                'type' => Type::string(),
                'description' => 'The description of the order document'
            ],
            'act' => [
                'type' => Type::string(),
                'description' => 'The act of the order document'
            ],
            'cdate' => [
                'type' => Type::string(),
                'description' => 'The cdate of the order document'
            ],
        ];
    }
}