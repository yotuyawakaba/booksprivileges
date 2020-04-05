<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = ['store_id', 'date', 'title', 'publisher', 'privilege_url', 'show_url'];
    
    public function Store()
    {
        return $this->belongsTo(Store::class);
    }
}
