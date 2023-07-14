<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Exceptions\APIException;
use App\Role;

class Authenticate extends Middleware
{
    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        parent::authenticate($request, $guards);
        /*$user = $this->auth->user();
        $needCheck = !((count($user->roles) == 2) && $user->hasAnyRole(Role::CLIENT) && $user->hasAnyRole(Role::CLIENTDASHBORD));

        if ($needCheck) {

            if ($user->sites) {

                foreach ($user->sites as $s) {

                    if (config('common.siteId', 1) == $s->cmf_site_id) {

                        $needCheck = false;
                        break;
                    }
                }
            }

            if ($needCheck) {

                throw new APIException('У вас нет прав для доступа к этой странице');
            }
        }*/
        //return CmfPrivilege::hasByRoute(Auth::user()->user_id, 'GraphQL_'.$this->__get('name'));
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
