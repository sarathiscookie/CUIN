<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
//use Illuminate\Database\Eloquent\Model;

class Customer extends Authenticatable
{
    protected $guarded = ['id', 'company_id'];

    public function customerCompany()
    {
        return $this->belongsTo('App\Company');
    }

}
