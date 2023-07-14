<?php
namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\GraphQL\RguysQuery;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\HasMiddleware;

class UserQuery extends RguysQuery
{
    use HasMiddleware;

    protected $attributes = [
        'name' => 'user'
    ];

    protected $middleware = [
        'auth:api'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('User'));
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::string()],
            'email' => ['name' => 'email', 'type' => Type::string()],
            'phone' => ['name' => 'phone', 'type' => Type::string()]
        ];
    }

    public function resolve($root, $args)
    {
        $this->handleMiddleware();

        if (isset($args['id'])) {

            return User::where('user_id' , $args['id'])->get();

        } elseif(isset($args['email'])) {

            return User::where('email', $args['email'])->get();

        } elseif(isset($args['phone'])) {

            return User::where('phone', $args['phone'])->get();

        } else {

            return  User::where('user_id', Auth::user()->getAuthIdentifier())->get();

        }
    }
}