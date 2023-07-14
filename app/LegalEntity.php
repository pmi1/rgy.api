<?php

namespace App;

use App\CmfSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LegalEntity extends Model
{

    public $table = 'legal_entities';

    public $primaryKey = 'id';

    public function cmfSite()
    {
        return $this->belongsTo(CmfSite::class, 'cmf_site_id', 'cmf_site_id');
    }
}