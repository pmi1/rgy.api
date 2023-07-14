<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderStatusComment extends Model
{
    public $table = 'orders_status_comment';

    protected $fillable = ['comment'];
}