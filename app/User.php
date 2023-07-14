<?php

namespace App;

use App\Order;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    public $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status', 'comment', 'lastname', 'discounter_id', 'secondname', 'companyname', 'discount', 'bonus_points', 'phone', 'lico', 'user_status_id', 'pol', 'siteurl', 'subscriber', 'company_office_phone', 'company_doljnost', 'company_rs', 'company_bank_name', 'company_ks', 'company_bic', 'companyname', 'company_ogrn', 'company_inn', 'company_kpp', 'company_real_address', 'person_seria', 'person_nomer', 'person_date', 'person_vidan', 'person_propiska', 'person_date_birthdday', 'person_mesto_rojdenia', 'company_ur_address', 'is_email_activation', 'is_subscribe', 'is_subscribe_sms', 'user_category_id', 'user_category_fixed', 'doc_name', 'doc_lastname', 'doc_secondname', 'main_user', 'company'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public $primaryKey = 'user_id';

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'cmf_user_role_link', 'user_id',  'cmf_role_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function log()
    {
        return $this->hasMany(OrderStatusLog::class, 'user_id', 'user_id');
    }

    public function cashbackLog()
    {
        return $this->hasMany(UserCashback::class, 'user_id', 'user_id');
    }

    public function executeOrders()
    {
        return $this->hasMany(Order::class, 'operator_id', 'user_id');
    }

    public function discounter()
    {
        return $this->belongsTo(Discounter::class, 'discounter_id',  'discounter_id');
    }

    public function userCategory()
    {
        return $this->belongsTo(UserCategory::class, 'user_category_id', 'user_category_id');
    }

    public function boss()
    {
        return $this->belongsTo(User::class, 'main_user', 'user_id');
    }

    public function staff()
    {
        return $this->hasMany(User::class, 'main_user', 'user_id');
    }

    public function sites()
    {
        return $this->hasManyThrough(CmfSite::class, UserCmfSite::class, 'user_id', 'cmf_site_id', 'user_id', 'cmf_site_id');
    }


    public function getOrderCountAttribute()
    {
        $result = 0;

        if ($this->user_id) {

            $result = DB::table('orders')->where('user_id', $this->user_id)->count();
        }

        return $result;
    }

    public function getDoneOrderSumCompanyAttribute()
    {
        $result = 0;

        if ($this->user_id) {

            $result = DB::table('user')
                ->where(function ($query) {
                    $query->where('user_id', $this->user_id)
                        ->orWhere('main_user', $this->user_id);
                })
                ->sum('doneOrderSum');
        }

        return $result;
    }

    public function getSimilarUsersAttribute()
    {
        $result = null;

        if ($this->email || $this->phone) {

            $result = User::whereRaw('(email = ? or phone = ?) and user_id <> ?', [$this->email, $this->phone, $this->user_id])
                ->get();
        }

        return $result;
    }

    public function authorizeRoles($roles)
    {
        if (is_array($roles)) {
            return $this->hasAnyRole($roles) ||
                abort(401, 'This action is unauthorized.');
        }
        return $this->hasRole($roles) ||
            abort(401, 'This action is unauthorized.');
    }

    /**
     * Check multiple roles
     * @param array $roles
     */
    public function hasAnyRole($roles)
    {
        return null !== $this->roles()->whereIn('code', $roles)->first();
    }
    /**
     * Check one role
     * @param string $role
     */
    public function hasRole($role)
    {
        return null !== $this->roles()->where('code', $role)->first();
    }

    public function accessTokens()
    {
        return $this->hasMany('App\OauthAccessToken');
    }

    public function hasContractor()
    {
        return $this->hasAnyRole(Role::CONTRACTOR_ROLE_CODES);
    }

    public function hasSupermanager()
    {
        return $this->hasAnyRole(Role::SUPERMANAGER_ROLE_CODES);
    }

    public function hasPid()
    {
        return $this->hasAnyRole(Role::PID_ROLE_CODES);
    }

    public function hasManager()
    {
        return $this->hasAnyRole(Role::MANAGER_ROLE_CODES);
    }

    public function item($id)
    {
        $result = DB::table('user as u')
            ->select([
                'u.user_id as id',
                'u.name',
                'u.lastname as lastName',
                'u.secondname as secondName',
                'u.email',
                'u.phone',
                'u.discount',
                'u.lico as type',
                'u.discounter_id as category',
                'u.companyname as company',
                'u.comment as description',
                'u.user_status_id as status',
                'u.pol as gender',
                'u.siteurl as companySite',
                'u.subscriber as companySubscriber',
                'u.company_office_phone as companyPhone',
                'u.company_doljnost as companyPost',
                'u.company_rs as companyCheckingAccount',
                'u.company_bank_name as companyBank',
                'u.company_ks as companyKs',
                'u.company_bic as companyBic',
                'u.companyname as companyName',
                'u.company_ogrn as companyOgrn',
                'u.company_inn as companyInn',
                'u.company_kpp as companyKpp',
                'u.company_real_address as companyActualAddress',
                'u.company_ur_address as companyLegalAddress',
                'u.person_seria as passportSeries',
                'u.person_nomer as passportNumber',
                'u.person_date as passportDate',
                'u.person_vidan as passportIssued',
                'u.person_propiska as passportRegistration',
                'u.person_date_birthdday as birthdday',
                'u.person_mesto_rojdenia as bornCity',
                'u.is_subscribe_sms as smsNotifications',
                'u.is_subscribe as emailNotifications',
                'u.exchangeNotifications',
                'o.ordered',
                'o.done',
            ])
            ->leftJoin(DB::raw('(select user_id
                    , count(DISTINCT orders.orders_id) as ordered
                    , sum(IF(FIND_IN_SET(orders.orders_status_id, "'.implode(',', Order::doneStatus).'") > 0, 1, 0)) as done 
                from `orders` group by user_id) as o'), 'o.user_id', '=', 'u.user_id')
            ->where('u.user_id', $id)
            ->first();

        return $result;
    }

}