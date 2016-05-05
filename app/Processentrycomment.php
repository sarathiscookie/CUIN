<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Processentrycomment extends Model
{
    protected $table = 'process_entry_comments';

    protected $guarded = ['id', 'process_entry_id'];
    public $timestamps = false;

    public static function boot()
    {
        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function processCommentProcessEntry()
    {
        return $this->belongsTo('App\Processentry');
    }
}
