<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const CONTRACTOR_ROLE_CODES = ['contractor'];
    const SUPERMANAGER_ROLE_CODES = ['supermanager'];
    const PID_ROLE_CODES = ['pid'];
    const MANAGER_ROLE_CODES = ['ordersmanager', 'supermanager', 'exchagemanager'];
    const LOGIST_CODES = ['logistician'];
    const WORKSHOP = ['workshop', 'cto'];
    const WAREHOUSE_CODES = ['warehouseoperator', 'cto'];
    const DRIVER = ['driver'];
    const HELPER = ['helper'];
    const CLIENT = ['userclient'];
    const CLIENTDASHBORD = ['userdisallowpart'];

    public $table = 'cmf_role';

    public $primaryKey = 'cmf_role_id';

    public function users()
    {
        return $this->belongsToMany(User::class, 'cmf_user_role_link', 'cmf_role_id', 'user_id');
    }

    public static function getIdByCode($code) {
        $role = Role::where('code', $code)->firstOrFail();

        return $roleId = $role ? $role->cmf_role_id : 0;
    }
}
