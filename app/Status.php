<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $guarded = ['id', 'company_id'];

    public $timestamps = false;

    public function statusCompany()
    {
        return $this->belongsTo('App\Company');
    }

    public function process()
    {
        return $this->hasMany('App\Process', 'status_id');
    }
}
