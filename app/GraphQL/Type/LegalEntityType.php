<?php
namespace App\GraphQL\Type;

use App\LegalEntity;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class LegalEntityType extends GraphQLType
{
    protected $attributes = [
        'name' => 'LegalEntity',
        'description' => 'A legal entity',
        'model' => LegalEntity::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of legal entity',
            ],
            'site' => [
                'type' => Type::string(),
                'description' => 'The Site of legal entity',
                'alias' => 'siteurl',
            ],
            'cmfSite' => [
                'type' => GraphQL::type('CmfSite'),
            ],
            'checkingAccount' => [
                'type' => Type::string(),
                'description' => 'The CheckingAccount of legal entity',
                'alias' => 'rs',
            ],
            'bank' => [
                'type' => Type::string(),
                'description' => 'The Bank of legal entity',
                'alias' => 'bank_name',
            ],
            'ks' => [
                'type' => Type::string(),
                'description' => 'The Ks of legal entity',
                'alias' => 'ks',
            ],
            'bic' => [
                'type' => Type::string(),
                'description' => 'The Bic of legal entity',
                'alias' => 'bic',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The Name of legal entity',
                'alias' => 'name',
            ],
            'ogrn' => [
                'type' => Type::string(),
                'description' => 'The Ogrn of legal entity',
                'alias' => 'ogrn',
            ],
            'inn' => [
                'type' => Type::string(),
                'description' => 'The Inn of legal entity',
                'alias' => 'inn',
            ],
            'kpp' => [
                'type' => Type::string(),
                'description' => 'The Kpp of legal entity',
                'alias' => 'kpp',
            ],
            'actualAddress' => [
                'type' => Type::string(),
                'description' => 'The ActualAddress of legal entity',
                'alias' => 'real_address',
            ],
            'legalAddress' => [
                'type' => Type::string(),
                'description' => 'The LegalAddress of legal entity',
                'alias' => 'ur_address',
            ],

        ];
    }
}