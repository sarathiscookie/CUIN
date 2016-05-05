<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use Billable;
    /** Eloquent expects created_at and updated_at columns to exist on your tables.
     * If you do not wish to have these columns automatically managed by Eloquent, set the $timestamps property
     * on your model to false*/
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'lastname', 'email', 'password', 'phone', 'street', 'postal', 'city', 'state', 'country',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function userCompany()
    {
        return $this->belongsTo('App\Company','company_id');
    }
}
