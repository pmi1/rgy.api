<?php

namespace App;

use App\UserCategory;
use Illuminate\Database\Eloquent\Model;

class ItemUserCategory extends Model
{
    public $table = 'item_user_category';

    public $primaryKey = 'item_user_category_id';

    public function userCategory()
    {
        return $this->belongsTo(UserCategory::class, 'user_category_id',  'user_category_id');
    }
}
