<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Processentryhistory extends Model
{
    protected $table = 'process_entry_histories';

    protected $guarded = ['id', 'process_entry_id'];
    public $timestamps = false;

    public static function boot()
    {
        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function processHistoryProcessEntry()
    {
        return $this->belongsTo('App\Processentry');
    }
}
