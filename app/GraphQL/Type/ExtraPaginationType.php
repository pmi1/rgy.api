<?php
namespace App\GraphQL\Type;

use Illuminate\Support\Collection;
use GraphQL\Type\Definition\ObjectType;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Pagination\LengthAwarePaginator;
use Rebing\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\PaginationType;

class ExtraPaginationType extends PaginationType
{
    protected function getPaginationFields(string $typeName): array
    {
        $result = parent::getPaginationFields($typeName);

        $result['totalCharge'] = [
                'type'          => Type::nonNull(Type::int()),
                'description'   => 'Price of total items selected by the query',
                'resolve'       => function (LengthAwarePaginator $data): int {
                    return $data->sum('orders.price');
                },
                'selectable'    => false,
        ];

        return $result;
    }
}
