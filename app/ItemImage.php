<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ItemImage extends Model
{
    public $primaryKey = 'item_image_id';

    public $table = 'item_image';

}