<?php

namespace App\Http\Controllers\API;

use App\Role;
use App\Order;
use App\CmfUserRoleLink;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\APIException;
use Laravel\Passport\Passport;
use Validator;

class UserController extends APIController
{
    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        $salt = env('HASH_SALT', '');
        $user = User::where('email', request('email'))
            ->where('password', md5($salt . request('password')))
            ->first();


        if ($user) {
            Auth::login($user);

            $success['token'] = $user->createToken('rguys')->accessToken;
            $success['user_id'] = $user->user_id;
            $success['roles'] = $user->roles()->get();
            return response()->json(['success' => $success], $this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function loginByEmail() {
        $salt = env('HASH_SALT', '');
        $hashEmail = request('hash');
        $user = User::whereRaw(sprintf('md5(CONCAT("%s", email)) = "%s"', $salt, $hashEmail))->first();
        if ($user) {
            Auth::login($user);

            $success['token'] = $user->createToken('rguys')->accessToken;
            $success['user_id'] = $user->user_id;
            $success['roles'] = $user->roles()->get();
            return response()->json(['success' => $success], $this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $salt = env('HASH_SALT', '');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = md5($salt.$input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('rguys')->accessToken;
        $success['name'] = $user->name;
        return response()->json(['success' => $success], $this->successStatus);
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        /**
         * @var \App\User
         */
        $user = Auth::user();

        $type = 'customer';

        if (!empty($user->contractor_id))
        {
            $type = 'contractor';
        }
        elseif (!empty($user->stand_id))
        {
            $type = 'platform';
        }

        $result = [
            'id' => $user->getAuthIdentifier(),
            'name' => $user->name,
            'lastname' => $user->lastname,
            'status' => $user->status,
            'type' => $type,
            'image' => $user->image,
            'email' => $user->email,
            'phone' => $user->phone,
            'roles' => $user->roles()->get()
        ];

        if ($type == 'platform')
        {
            $result = array_merge($result, [
                'theme' => $user->theme,
                'fonts' => $user->fonts,
                'pid' => $user->stand_id,
            ]);
        }

        return response()->json(['success' => $result], $this->successStatus);
    }


    /**
     * customers api
     *
     * @return \Illuminate\Http\Response
     */
    public function customers(Request $request)
    {
        /**
         * @var \App\User
         */
        $user = Auth::user();

        if (!$user->hasRole('supermanager') && !$user->hasManager()) {
            throw new APIException('У вас нет прав для доступа к этой стринице');
        }

        $query = $request->get('query');

        $queryBuilder = DB::table('user')
            ->select([
                DB::raw('user.user_id as id'),
                DB::raw('user.name'),
                DB::raw('user.lastname as lastName'),
                DB::raw('user.secondname as secondName'),
                DB::raw('user.email'),
                DB::raw('user.phone'),
                DB::raw('user.discount'),
                DB::raw('user.discounter_id as category'),
                DB::raw('user.companyname as company'),
                DB::raw('user.comment as description'),
                DB::raw('o.ordered'),
                DB::raw('o.done'),
            ])
            ->leftJoin(DB::raw('(select user_id
                    , count(DISTINCT orders.orders_id) as ordered
                    , sum(IF(FIND_IN_SET(orders.orders_status_id, "'.implode(',', Order::doneStatus).'") > 0, 1, 0)) as done 
                from `orders` group by user_id) as o'), 'o.user_id', '=', 'user.user_id')
            ->whereRaw($query ? "
                (user.user_id = '$query' or user.phone like '%$query%' or user.email like '%$query%'
                     or user.name like '%$query%' or user.secondname like '%$query%' or user.lastname like '%$query%' 
                     or concat(user.lastname, ' ', user.name, ' ', user.secondname) like '%$query%')
            " : 'true');

        if ($discounters = $request->get('discounters')) {
            $discounters = json_decode($discounters, true);
            $queryBuilder->whereIn('discounter_id', $discounters);
        }

        if ($discount = $request->get('discount')) {
            $queryBuilder->where(function($query) use ($discount){
                $query->orWhere('discount', '<=', $discount);
                $query->orWhereNull('discount');
            });
        }

        if ($request->get('sortType', 'id') && $request->get('sortDir', 'desc')) {

            $queryBuilder->orderBy($request->get('sortType', 'id'), $request->get('sortDir', 'desc'));
        }

        $pagination = $queryBuilder->paginate(env('PAGINATION_COUNT_PER_PAGE'));

        return response()->json(['success' => $pagination], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function managers(Request $request) {
        /**
         * @var \App\User
         */
        $user = Auth::user();
        if (!$user->hasManager() && !$user->hasPid() && !$user->hasSupermanager()) {
            throw new APIException('У вас нет прав для доступа к этой стринице');
        }

        $managers = User::whereHas('roles', function ($query){
            $query->whereIn('code', Role::MANAGER_ROLE_CODES);
        })
            ->get([
                'user_id as id',
                'email',
                'phone',
                'name',
                'lastname as lastName',
                'secondname as secondName',
                'mango_user_id'
            ]);

        $managers = array_map(function($item){
            $item['lastName'] = empty($item['lastName'])?null:$item['lastName'];
            $item['secondName'] = empty($item['secondName'])?null:$item['secondName'];
            return $item;
        }, $managers->toArray());

        return response()->json(['success' => $managers], $this->successStatus, [], JSON_NUMERIC_CHECK);

    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'success' => 'Successfully logged out'
        ],
            $this->successStatus, [], JSON_NUMERIC_CHECK);
    }

    public function helpers() {
        $helpers = DB::table('user')
            ->leftJoin('cmf_user_role_link', 'cmf_user_role_link.user_id', '=', 'user.user_id')
            ->where('cmf_user_role_link.cmf_role_id', '28')
            ->get([
               'user.user_id',
               'user.name',
               'user.email',
               'user.phone',
            ]);

        return response()->json(['success' => $helpers], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }


    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function customer(Request $request)
    {
        /**
         * @var \App\User
         */
        $user = Auth::user();

        if (!($user->user_id == $request->get('userId')
            || $user->user_id == $request->get('id')
            || $user->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES, Role::CONTRACTOR_ROLE_CODES, Role::PID_ROLE_CODES)))) {

            throw new APIException('У вас нет прав для доступа к этой странице');
        }

        $result = false;
        $userObject = new User();

        if ($request->get('userId')) {

            $result = $userObject->item($request->get('userId'));
        } elseif ($request->get('id') && ($userObject = User::find($request->get('id')))) {

            $this->validate($request, [
                'email' => 'required|email|unique:user,email,'.$request->get('id').',user_id|max:255',
                'phone' => 'required|max:255',
                'name' => 'required',
                'discount' => 'integer|min:0|max:99',
                'lastName' => 'required'
            ]);

            $fieldMaps = [
                'name' => 'name',
                'lastName' => 'lastname',
                'secondName' => 'secondname',
                'email' => 'email',
                'phone' => 'phone',
                'discount' => 'discount',
                'type' => 'lico',
                'category' => 'discounter_id',
                'company' => 'company',
                'description' => 'comment',
                'status' => 'user_status_id',
                'gender' => 'pol',
                'companySite' => 'siteurl',
                'companySubscriber' => 'subscriber',
                'companyPhone' => 'company_office_phone',
                'companyPost' => 'company_doljnost',
                'companyCheckingAccount' => 'company_rs',
                'companyBank' => 'company_bank_name',
                'companyKs' => 'company_ks',
                'companyBic' => 'company_bic',
                'companyName' => 'companyname',
                'companyOgrn' => 'company_ogrn',
                'companyInn' => 'company_inn',
                'companyKpp' => 'company_kpp',
                'companyActualAddress' => 'company_real_address',
                'companyLegalAddress' => 'company_ur_address',
                'passportSeries' => 'person_seria',
                'passportNumber' => 'person_nomer',
                'passportDate' => 'person_date',
                'passportIssued' => 'person_vidan',
                'passportRegistration' => 'person_propiska',
                'birthdday' => 'person_date_birthdday',
                'bornCity' => 'person_mesto_rojdenia',
                'smsNotifications' => 'is_subscribe_sms',
                'emailNotifications' => 'is_subscribe',
                'exchangeNotifications' => 'exchangeNotifications',
                'userCategory' => 'user_category_id',
                'userCategoryFixed' => 'user_category_fixed',
                'docFirstName' => 'doc_name',
                'docLastName' => 'doc_lastname',
                'docSecondName' => 'doc_secondname',
                'boss' => 'main_user',
            ];

            foreach ($request->all() as $key=>$val) {

                if (isset($fieldMaps[$key])) {
                    $userObject[$fieldMaps[$key]] = $val;
                }
            }

            $userObject->save();

            $result = true;
        }

        return response()->json(['success' => $result], $this->successStatus, []);
    }

    /**
     * customers api
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        /**
         * @var \App\User
         */
        $answer = [];
        $user = Auth::user();

        if (!$user->hasRole('supermanager') && !$user->hasManager()) {
            throw new APIException('У вас нет прав для доступа к этой стринице');
        }

        if (($email = $request->get('email')) && ($phone = $request->get('phone'))) {

            $queryBuilder = DB::table('user')
                ->select([
                    DB::raw('user.user_id as id'),
                    DB::raw('user.name'),
                    DB::raw('user.lastname as lastName'),
                    DB::raw('user.secondname as secondName'),
                    DB::raw('user.email'),
                    DB::raw('user.phone'),
                    DB::raw('user.discount'),
                    DB::raw('user.discounter_id as category'),
                    DB::raw('user.companyname as company'),
                    DB::raw('user.comment as description'),
                    DB::raw('o.ordered'),
                    DB::raw('o.done'),
                ])
                ->leftJoin(DB::raw('(select user_id
                        , count(DISTINCT orders.orders_id) as ordered
                        , sum(IF(FIND_IN_SET(orders.orders_status_id, "'.implode(',', Order::doneStatus).'") > 0, 1, 0)) as done 
                    from `orders` group by user_id) as o'), 'o.user_id', '=', 'user.user_id')
                ->whereRaw("user.email = '$email' or user.phone = '$phone'")
                ->orderBy('id', 'desc');

            $pagination = $queryBuilder->paginate(env('PAGINATION_COUNT_PER_PAGE'));

            if ($pagination->total()) {

                $answer = ['success' => $pagination];
            } else {

                $salt = env('HASH_SALT', '');

                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:user',
                    'phone' => 'required|max:255|unique:user',
                ]);

                if ($validator->fails()) {

                    $answer = ['error' => $validator->errors()];
                } else {

                    $input = $request->all();
                    $input = array_merge($input, [
                        'is_email_activation' => 1,
                        'status' => 1,
                        'is_subscribe' => 1,
                        'is_subscribe_sms' => 1]);
                    $user = User::create($input);

                    $role = ['user_id' => $user->user_id, 'cmf_role_id' => 21];
                    $role = CmfUserRoleLink::create($role);
                    $role = ['user_id' => $user->user_id, 'cmf_role_id' => 22];
                    $role = CmfUserRoleLink::create($role);

                    $answer = ['user' => $user];
                }
            }
        }

        return response()->json($answer, $this->successStatus, [], JSON_NUMERIC_CHECK);
    }
}