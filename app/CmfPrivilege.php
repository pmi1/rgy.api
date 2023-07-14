<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CmfPrivilege extends Model
{
    const VIEW      = 'view';
    const ADD       = 'add';
    const DELETE    = 'delete';
    const EDIT      = 'edit';

    const RIGHTS_TABLE_NAME = 'rights_table';
    
    static private $_rights = null;

    public $table = 'cmf_privilege';

#------------------------------------------------------------------------------

    static public function isAllowed($user_id, $page_id, $privelege)
    {
        $result = false;
        if ( !self::$_rights) {
            self::_setRights($user_id, $page_id);
        }
        $count = DB::select('SELECT COUNT(*) AS count FROM '.self::RIGHTS_TABLE_NAME.' WHERE user_id = ? AND cmf_script_id = ? AND privilege = ?', array($user_id, $page_id, $privelege) );
        
        if ( intval($count) > 0 ) {

            $result = true;
        }

        return $result;
    }

#------------------------------------------------------------------------------

    static public function getRights($user_id, $page_id)
    {
        $result = array();

        $priveleges = DB::select('SELECT privilege FROM '.self::RIGHTS_TABLE_NAME.' WHERE user_id = ? AND cmf_script_id = ?', array($user_id, $page_id) );
        $index = sizeof($priveleges);
        for($i=0; $i<$index; ++$i)
        {
            $result[$priveleges[$i]->privilege] = 1;
        }

        return $result;
    }

#------------------------------------------------------------------------------

    static public function buildRightsTable( $user_id )
    {
        DB::statement('CREATE TEMPORARY TABLE IF NOT EXISTS '.self::RIGHTS_TABLE_NAME.' (user_id int not null, cmf_script_id int not null, privilege VARCHAR(255), primary key(user_id, cmf_script_id, privilege) )');

        DB::delete('DELETE FROM '.self::RIGHTS_TABLE_NAME.' WHERE user_id = ?', [$user_id]);

        DB::insert('INSERT IGNORE INTO
                                    '.self::RIGHTS_TABLE_NAME.'
                            SELECT
                                    rl.user_id,
                                    ms.cmf_script_id,
                                    p.sysname
                            FROM
                                    cmf_user_role_link rl
                            INNER JOIN
                                    cmf_role r
                                ON
                                    rl.cmf_role_id  = r.cmf_role_id
                            INNER JOIN
                                    cmf_role_module_privilege rmp
                                ON
                                    rmp.cmf_role_id = r.cmf_role_id
                            INNER JOIN
                                    cmf_privilege p
                                ON
                                    p.cmf_privilege_id = rmp.cmf_privilege_id
                            INNER JOIN
                                    module_scripts ms
                                ON
                                    ms.module_id = rmp.module_id
                            INNER JOIN
                                    module m
                                ON
                                    m.module_id = ms.module_id
                            WHERE
                                rl.user_id = ?' , [$user_id]);
    }

#-----------------------------------------------------------------------------

    static public function has($user_id, $page_id, $privilege)
    {
        $result = false;

        if ( !self::$_rights ) {
            self::_setRights($user_id, $page_id);
        }

        if ( isset(self::$_rights[$privilege]) && ( 1 == self::$_rights[$privilege] ) )
        {
            $result = true;
        }

        return $result;
    }

#-----------------------------------------------------------------------------

    static public function hasByRoute($userId, $route)
    {
        $result = false;
        if ( !self::$_rights ) {

            $page = DB::table('cmf_script')
                ->where('cmf_site_id', 1)
                ->where('article', $route)->first();

            if ($page) {

                self::_setRights($userId, $page->cmf_script_id);
            }
        }

        if ( isset(self::$_rights[self::VIEW]) && ( 1 == self::$_rights[self::VIEW] ) )
        {
            $result = true;
        }

        return $result;
    }

#-----------------------------------------------------------------------------

    static protected function _setRights($user_id, $page_id)
    {
        self::buildRightsTable($user_id);
        self::$_rights = self::getRights($user_id, $page_id);
    }

#-----------------------------------------------------------------------------

}