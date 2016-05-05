<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Processentry extends Model
{
    protected $table = 'process_entries';

    protected $guarded = ['id', 'process_id'];

    public function processEntryProcess()
    {
        return $this->belongsTo('App\Process');
    }

    public function comments()
    {
        return $this->hasMany('App\Processentrycomment', 'process_entry_id');
    }
}
