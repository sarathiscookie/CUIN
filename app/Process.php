<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $guarded = ['id', 'company_id', 'customer_id', 'status_id'];

    public function processCompany()
    {
        return $this->belongsTo('App\Company');
    }

    public function processCustomer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function processStatus()
    {
        return $this->belongsTo('App\Status');
    }
}
