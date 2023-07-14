<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CmfSiteType extends GraphQLType
{
    protected $attributes = [
        'name' => 'CmfSite',
        'description' => 'A CmfSite status'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the CmfSite',
                'alias' => 'cmf_site_id'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the CmfSite'
            ],
            'ordering' => [
                'type' => Type::string(),
                'description' => 'The ordering of the CmfSite'
            ],
            'isDefault' => [
                'type' => Type::string(),
                'description' => 'The ordering of the CmfSite',
                'alias' => 'is_default'
            ],
            'systemName' => [
                'type' => Type::string(),
                'description' => 'The alias of the CmfSite',
                'alias' => 'system_name'
            ],
            'url' => [
                'type' => Type::string(),
                'description' => 'The url of the CmfSite',
                'alias' => DB::raw('REPLACE("https://#rguys.pro", "#", IF(is_default, "", concat(system_name, "."))) as url')
            ],
            'status' => [
                'type' => Type::string(),
                'description' => 'The status of the CmfSite'
            ]
        ];
    }
}