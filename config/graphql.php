<?php


use example\Type\ExampleType;
use example\Query\ExampleQuery;
use example\Mutation\ExampleMutation;
use example\Type\ExampleRelationType;

return [

    // The prefix for routes
    'prefix' => 'graphql',

    // The routes to make GraphQL request. Either a string that will apply
    // to both query and mutation or an array containing the key 'query' and/or
    // 'mutation' with the according Route
    //
    // Example:
    //
    // Same route for both query and mutation
    //
    // 'routes' => 'path/to/query/{graphql_schema?}',
    //
    // or define each route
    //
    // 'routes' => [
    //     'query' => 'query/{graphql_schema?}',
    //     'mutation' => 'mutation/{graphql_schema?}',
    // ]
    //
    'routes' => '{graphql_schema?}',

    // The controller to use in GraphQL request. Either a string that will apply
    // to both query and mutation or an array containing the key 'query' and/or
    // 'mutation' with the according Controller and method
    //
    // Example:
    //
    // 'controllers' => [
    //     'query' => '\Rebing\GraphQL\GraphQLController@query',
    //     'mutation' => '\Rebing\GraphQL\GraphQLController@mutation'
    // ]
    //
    'controllers' => \Rebing\GraphQL\GraphQLController::class.'@query',

    // Any middleware for the graphql route group
    'middleware' => ['cors', 'auth:api'],

    // Additional route group attributes
    //
    // Example:
    //
    // 'route_group_attributes' => ['guard' => 'api']
    //
    'route_group_attributes' => [],

    // The name of the default schema used when no argument is provided
    // to GraphQL::schema() or when the route is used without the graphql_schema
    // parameter.
    'default_schema' => 'default',

    // The schemas for query and/or mutation. It expects an array of schemas to provide
    // both the 'query' fields and the 'mutation' fields.
    //
    // You can also provide a middleware that will only apply to the given schema
    //
    // Example:
    //
    //  'schema' => 'default',
    //
    //  'schemas' => [
    //      'default' => [
    //          'query' => [
    //              'users' => 'App\GraphQL\Query\UsersQuery'
    //          ],
    //          'mutation' => [
    //
    //          ]
    //      ],
    //      'user' => [
    //          'query' => [
    //              'profile' => 'App\GraphQL\Query\ProfileQuery'
    //          ],
    //          'mutation' => [
    //
    //          ],
    //          'middleware' => ['auth'],
    //      ],
    //      'user/me' => [
    //          'query' => [
    //              'profile' => 'App\GraphQL\Query\MyProfileQuery'
    //          ],
    //          'mutation' => [
    //
    //          ],
    //          'middleware' => ['auth'],
    //      ],
    //  ]
    //
    'schemas' => [
        'default' => [
            'query' => [
                App\GraphQL\Query\ContractorQuery::class,
                App\GraphQL\Query\CashbackOperationQuery::class,
                App\GraphQL\Query\CashbackTypeQuery::class,
                App\GraphQL\Query\ManagersQuery::class,
                App\GraphQL\Query\OrderStatusesQuery::class,
                App\GraphQL\Query\PlatformsQuery::class,
                App\GraphQL\Query\OrderPaymentStatusesQuery::class,
                App\GraphQL\Query\OrderCancelReasonsQuery::class,
                App\GraphQL\Query\DiscountersQuery::class,
                App\GraphQL\Query\UserStatusesQuery::class,
                App\GraphQL\Query\TodoTypeQuery::class,
                App\GraphQL\Query\TodoStatusesQuery::class,
                App\GraphQL\Query\TodoStatesQuery::class,
                App\GraphQL\Query\TodoPrioritiesQuery::class,
                App\GraphQL\Query\CallTypesQuery::class,
                App\GraphQL\Query\CallRatingsQuery::class,
                App\GraphQL\Query\UserCategoryQuery::class,
                App\GraphQL\Query\UserQuery::class,
                App\GraphQL\Query\UsersQuery::class,
                App\GraphQL\Query\OrderEventTypeQuery::class,
                App\GraphQL\Query\OrderGoalQuery::class,
                App\GraphQL\Query\OrderRentRefundReasonQuery::class,
                App\GraphQL\Query\OrderServiceRefundReasonQuery::class,
                App\GraphQL\Query\ProductQuery::class,
                App\GraphQL\Query\ProductUserCategoryQuery::class,
                App\GraphQL\Query\UserCashbackQuery::class,
                App\GraphQL\Query\OrderTotalChargeQuery::class,
                App\GraphQL\Query\LegalEntityQuery::class,
                App\GraphQL\Query\OrdersQuery::class,
                App\GraphQL\Query\TaxQuery::class,
                App\GraphQL\Query\OrderPaymentQuery::class,
                App\GraphQL\Query\OrderLogistQuery::class,
                App\GraphQL\Query\OrderComplectQuery::class,
                App\GraphQL\Query\PaymentStatusQuery::class,
                App\GraphQL\Query\PaymentTypeQuery::class,
                App\GraphQL\Query\PaymentVariantQuery::class,
                App\GraphQL\Query\SberPaymentQuery::class,
                App\GraphQL\Query\CmfSiteQuery::class,
                App\GraphQL\Query\DriverQuery::class,
                App\GraphQL\Query\HelperQuery::class,
                App\GraphQL\Query\CarQuery::class,
            ],
            'mutation' => [
                App\GraphQL\Mutation\ChangeUserCashbackMutation::class,
                App\GraphQL\Mutation\OrderPaymentMutation::class,
                App\GraphQL\Mutation\OrderLogistMutation::class,
                App\GraphQL\Mutation\OrderLogistDriverMutation::class,
                App\GraphQL\Mutation\OrderLogistHelperMutation::class,
                App\GraphQL\Mutation\OrderLogistExecMutation::class,
                App\GraphQL\Mutation\ProductContractorMutation::class,
                App\GraphQL\Mutation\UserPromocodeMutation::class,
                App\GraphQL\Mutation\OrderComplectMutation::class,
                // 'example_mutation'  => ExampleMutation::class,
            ],
            'middleware' => [],
            'method'     => ['get', 'post'],
        ],
    ],

    // The types available in the application. You can then access it from the
    // facade like this: GraphQL::type('user')
    //
    // Example:
    //
    // 'types' => [
    //     'user' => 'App\GraphQL\Type\UserType'
    // ]
    //
    'types' => [
        'OrderLogistType' => App\GraphQL\Type\OrderLogistTypeEnum::class,
        App\GraphQL\Type\TaxType::class,
        App\GraphQL\Type\CashbackTypeType::class,
        App\GraphQL\Type\CashbackOperationType::class,
        App\GraphQL\Type\UserType::class,
        App\GraphQL\Type\PlatformType::class,
        App\GraphQL\Type\OrderStatusType::class,
        App\GraphQL\Type\OrderStatusLogType::class,
        App\GraphQL\Type\OrderPaymentStatusType::class,
        App\GraphQL\Type\OrderCancelReasonType::class,
        App\GraphQL\Type\DiscounterType::class,
        App\GraphQL\Type\UserCategoryType::class,
        App\GraphQL\Type\UserStatusType::class,
        App\GraphQL\Type\TodoTypeType::class,
        App\GraphQL\Type\TodoStatusType::class,
        App\GraphQL\Type\TodoStateType::class,
        App\GraphQL\Type\TodoPriorityType::class,
        App\GraphQL\Type\CallTypeType::class,
        App\GraphQL\Type\OrderEventTypeType::class,
        App\GraphQL\Type\OrderGoalType::class,
        App\GraphQL\Type\OrderRentRefundReasonType::class,
        App\GraphQL\Type\OrderServiceRefundReasonType::class,
        App\GraphQL\Type\CallRatingType::class,
        App\GraphQL\Type\UserRoleType::class,
        App\GraphQL\Type\OrderProductType::class,
        App\GraphQL\Type\ProductType::class,
        App\GraphQL\Type\ProductUserCategoryType::class,
        App\GraphQL\Type\ProductAccessoryType::class,
        App\GraphQL\Type\UserCashbackType::class,
        App\GraphQL\Type\OrderType::class,
        App\GraphQL\Type\OrderProductBusyType::class,
        App\GraphQL\Type\OrderProductBusyOrderType::class,
        App\GraphQL\Type\OrderProposalType::class,
        App\GraphQL\Type\ProductContractorType::class,
        App\GraphQL\Type\ContractorType::class,
        App\GraphQL\Type\OrderDocumentType::class,
        App\GraphQL\Type\LegalEntityType::class,
        App\GraphQL\Type\OrderPaymentType::class,
        App\GraphQL\Type\OrderLogistType::class,
        App\GraphQL\Type\OrderLogistHelperType::class,
        App\GraphQL\Type\OrderLogistDriverType::class,
        App\GraphQL\Type\OrderLogistExecType::class,
        App\GraphQL\Type\OrderLogistExecPhotoType::class,
        App\GraphQL\Type\OrderComplectType::class,
        App\GraphQL\Type\PaymentStatusType::class,
        App\GraphQL\Type\PaymentTypeType::class,
        App\GraphQL\Type\PaymentVariantType::class,
        App\GraphQL\Type\CmfSiteType::class,
        App\GraphQL\Type\CarType::class,
        App\GraphQL\Type\OrderAmountTaxType::class,
    ],

    // This callable will be passed the Error object for each errors GraphQL catch.
    // The method should return an array representing the error.
    // Typically:
    // [
    //     'message' => '',
    //     'locations' => []
    // ]
    'error_formatter' => ['\Rebing\GraphQL\GraphQL', 'formatError'],

    /*
     * Custom Error Handling
     *
     * Expected handler signature is: function (array $errors, callable $formatter): array
     *
     * The default handler will pass exceptions to laravel Error Handling mechanism
     */
    'errors_handler' => ['\Rebing\GraphQL\GraphQL', 'handleErrors'],

    // You can set the key, which will be used to retrieve the dynamic variables
    'params_key'    => 'variables',

    /*
     * Options to limit the query complexity and depth. See the doc
     * @ https://github.com/webonyx/graphql-php#security
     * for details. Disabled by default.
     */
    'security' => [
        'query_max_complexity'  => null,
        'query_max_depth'       => null,
        'disable_introspection' => false,
    ],

    /*
     * You can define your own pagination type.
     * Reference \Rebing\GraphQL\Support\PaginationType::class
     */
    'pagination_type' => \Rebing\GraphQL\Support\PaginationType::class,

    /*
     * Config for GraphiQL (see (https://github.com/graphql/graphiql).
     */
    'graphiql' => [
        'prefix'     => '/graphiql/{graphql_schema?}',
        'controller' => \Rebing\GraphQL\GraphQLController::class.'@graphiql',
        'middleware' => [],
        'view'       => 'graphql::graphiql',
        'display'    => env('ENABLE_GRAPHIQL', true),
    ],

    /*
     * Overrides the default field resolver
     * See http://webonyx.github.io/graphql-php/data-fetching/#default-field-resolver
     *
     * Example:
     *
     * ```php
     * 'defaultFieldResolver' => function ($root, $args, $context, $info) {
     * },
     * ```
     * or
     * ```php
     * 'defaultFieldResolver' => [SomeKlass::class, 'someMethod'],
     * ```
     */
    'defaultFieldResolver' => null,

    /*
     * Any headers that will be added to the response returned by the default controller
     */
    'headers' => [],

    /*
     * Any JSON encoding options when returning a response from the default controller
     * See http://php.net/manual/function.json-encode.php for the full list of options
     */
    'json_encoding_options' => 0,
];
