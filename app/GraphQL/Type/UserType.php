<?php
namespace App\GraphQL\Type;

use App\User;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserType extends GraphQLType
{
    protected $attributes = [
        'name' => 'User',
        'description' => 'A user',
        'model' => User::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the user',
                'alias' => 'user_id',
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'The email of user'
            ],
            'phone' => [
                'type' => Type::string(),
                'description' => 'The phone of user'
            ],
            'firstName' => [
                'type' => Type::string(),
                'description' => 'The first name of user',
                'alias' => 'name',
            ],
            'lastName' => [
                'type' => Type::string(),
                'description' => 'The last name of user',
                'alias' => 'lastname',
            ],
            'secondName' => [
                'type' => Type::string(),
                'description' => 'The second name of user',
                'alias' => 'secondname',
            ],
            'mangoUserId' => [
                'type' => Type::string(),
                'description' => 'The mango user id',
                'alias' => 'mango_user_id',
            ],
            'userCategory' => [
                'type' => GraphQL::type('UserCategory'),
                'description' => 'The user category'
            ],
            'boss' => [
                'type' => GraphQL::type('User'),
                'description' => 'The boss of user'
            ],
            'staff' => [
                'type' => Type::listOf(GraphQL::type('User')),
                'description' => 'The staff of user'
            ],
            'roles' => [
                'type' => Type::listOf(GraphQL::type('UserRole')),
                'description' => 'The user role',
                'always' => ['name'],
            ],
            'orders' => [
                'type' => Type::listOf(GraphQL::type('Order')),
                'description' => 'The user orders'
            ],
            'cashbackLog' => [
                'type' => Type::listOf(GraphQL::type('UserCashback')),
                'description' => 'The user cashback'
            ],
            'theme' => [
                'type' => Type::string(),
                'description' => 'The user theme'
            ],
            'fonts' => [
                'type' => Type::string(),
                'description' => 'The user fonts'
            ],
            'pid' => [
                'type' => Type::string(),
                'description' => 'The user pid',
                'alias' => 'stand_id'
            ],
            'discounter' => [
                'type' => GraphQL::type('Discounter'),
                'description' => 'The user discounter',
            ],
            'cashback' => [
                'type' => Type::string(),
                'description' => 'The user cashback',
            ],
            'discount' => [
                'type' => Type::string(),
                'description' => 'The user discount'
            ],
            'image' => [
                'type' => Type::string(),
                'description' => 'The user image',
            ],
            'orderCount' => [
                'type' => Type::string(),
                'description' => 'The user order count',
                'selectable' => false,
            ],
            'doneOrderCount' => [
                'type' => Type::string(),
                'description' => 'The user done order count',
            ],
            'doneOrderSum' => [
                'type' => Type::string(),
                'description' => 'The user done order sum',
            ],
            'doneOrderSumCompany' => [
                'type' => Type::string(),
                'description' => 'The user done order sum',
                'selectable' => false,
            ],
            'userCategoryFixed' => [
                'type' => Type::boolean(),
                'description' => 'The category fixed',
                'alias' => 'user_category_fixed',
            ],
            'docFirstName' => [
                'type' => Type::string(),
                'description' => 'The first name of user',
                'alias' => 'doc_name',
            ],
            'docLastName' => [
                'type' => Type::string(),
                'description' => 'The last name of user',
                'alias' => 'doc_lastname',
            ],
            'docSecondName' => [
                'type' => Type::string(),
                'description' => 'The second name of user',
                'alias' => 'doc_secondname',
            ],
            'type' => [
                'type' => Type::string(),
                'description' => 'The type of user',
                'alias' => 'lico',
            ],
            'company' => [
                'type' => Type::string(),
                'description' => 'The company of user',
            ],
            'description' => [
                'type' => Type::string(),
                'description' => 'The description of user',
                'alias' => 'comment',
            ],
            'status' => [
                'type' => Type::string(),
                'description' => 'The status of user',
                'alias' => 'user_status_id',
            ],
            'gender' => [
                'type' => Type::string(),
                'description' => 'The gender of user',
                'alias' => 'pol',
            ],
            'companySite' => [
                'type' => Type::string(),
                'description' => 'The companySite of user',
                'alias' => 'siteurl',
            ],
            'companySubscriber' => [
                'type' => Type::string(),
                'description' => 'The companySubscriber of user',
                'alias' => 'subscriber',
            ],
            'companyPhone' => [
                'type' => Type::string(),
                'description' => 'The companyPhone of user',
                'alias' => 'company_office_phone',
            ],
            'companyPost' => [
                'type' => Type::string(),
                'description' => 'The companyPost of user',
                'alias' => 'company_doljnost',
            ],
            'companyCheckingAccount' => [
                'type' => Type::string(),
                'description' => 'The companyCheckingAccount of user',
                'alias' => 'company_rs',
            ],
            'companyBank' => [
                'type' => Type::string(),
                'description' => 'The companyBank of user',
                'alias' => 'company_bank_name',
            ],
            'companyKs' => [
                'type' => Type::string(),
                'description' => 'The companyKs of user',
                'alias' => 'company_ks',
            ],
            'companyBic' => [
                'type' => Type::string(),
                'description' => 'The companyBic of user',
                'alias' => 'company_bic',
            ],
            'companyName' => [
                'type' => Type::string(),
                'description' => 'The companyName of user',
                'alias' => 'companyname',
            ],
            'companyOgrn' => [
                'type' => Type::string(),
                'description' => 'The companyOgrn of user',
                'alias' => 'company_ogrn',
            ],
            'companyInn' => [
                'type' => Type::string(),
                'description' => 'The companyInn of user',
                'alias' => 'company_inn',
            ],
            'companyKpp' => [
                'type' => Type::string(),
                'description' => 'The companyKpp of user',
                'alias' => 'company_kpp',
            ],
            'companyActualAddress' => [
                'type' => Type::string(),
                'description' => 'The companyActualAddress of user',
                'alias' => 'company_real_address',
            ],
            'companyLegalAddress' => [
                'type' => Type::string(),
                'description' => 'The companyLegalAddress of user',
                'alias' => 'company_ur_address',
            ],
            'passportSeries' => [
                'type' => Type::string(),
                'description' => 'The passportSeries of user',
                'alias' => 'person_seria',
            ],
            'passportNumber' => [
                'type' => Type::string(),
                'description' => 'The passportNumber of user',
                'alias' => 'person_nomer',
            ],
            'passportDate' => [
                'type' => Type::string(),
                'description' => 'The passportDate of user',
                'alias' => 'person_date',
            ],
            'passportIssued' => [
                'type' => Type::string(),
                'description' => 'The passportIssued of user',
                'alias' => 'person_vidan',
            ],
            'passportRegistration' => [
                'type' => Type::string(),
                'description' => 'The passportRegistration of user',
                'alias' => 'person_propiska',
            ],
            'birthdday' => [
                'type' => Type::string(),
                'description' => 'The birthdday of user',
                'alias' => 'person_date_birthdday',
            ],
            'bornCity' => [
                'type' => Type::string(),
                'description' => 'The bornCity of user',
                'alias' => 'person_mesto_rojdenia',
            ],
            'smsNotifications' => [
                'type' => Type::string(),
                'description' => 'The smsNotifications of user',
                'alias' => 'is_subscribe_sms',
            ],
            'emailNotifications' => [
                'type' => Type::string(),
                'description' => 'The emailNotifications of user',
                'alias' => 'is_subscribe',
            ],
            'exchangeNotifications' => [
                'type' => Type::string(),
                'description' => 'The exchangeNotifications of user',
                'alias' => 'exchangeNotifications',
            ],
            'image' => [
                'type' => Type::string(),
                'description' => 'The image of user',
            ],
            'promocode' => [
                'type' => Type::string(),
                'description' => 'The promocode',
            ],
            'similarUsers' => [
                'type' => Type::listOf(GraphQL::type('User')),
                'description' => 'The similar users of user',
                'selectable' => false,
            ],
        ];
    }
}