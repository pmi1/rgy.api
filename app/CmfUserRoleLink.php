<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CmfUserRoleLink extends Model
{
    public $table = 'cmf_user_role_link';

    public $primaryKey = 'cmf_user_role_link_id';

    protected $fillable = ['user_id', 'cmf_role_id'];

}