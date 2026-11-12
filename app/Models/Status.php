<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'Status';
    protected $primaryKey = 'Status_ID';
    public $timestamps = false;
    protected $guarded = [];

    public function werkzeuge(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Werkzeug::class, 'Status_ID');
    }
}
