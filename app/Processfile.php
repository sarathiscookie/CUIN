<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Processfile extends Model
{
    protected $table = 'process_files';

    protected $guarded = ['id', 'process_entry_id'];

    public function processFileProcessEntry()
    {
        return $this->belongsTo('App\Processentry');
    }
}
