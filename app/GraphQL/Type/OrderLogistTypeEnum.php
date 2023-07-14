<?php
namespace App\GraphQL\Type;

use Rebing\GraphQL\Support\EnumType;
use App\OrderLogist;

class OrderLogistTypeEnum extends EnumType
{
    protected $attributes = [
        'name' => 'OrderLogistType',
        'description' => 'The types of order logist',
        'values' => [
            'LOADING' => OrderLogist::LOADING,
            'UNLOADING' => OrderLogist::UNLOADING,
            'INSTALL' => OrderLogist::INSTALL,
            'UNINSTALL' => OrderLogist::UNINSTALL,
            'ASSIGNMENT' => OrderLogist::ASSIGNMENT,
        ],
    ];
}