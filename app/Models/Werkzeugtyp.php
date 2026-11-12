<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Werkzeugtyp extends Model
{
    protected $table = 'Werkzeugtyp';
    protected $primaryKey = 'Typ_ID';
    public $timestamps = false;
    protected $guarded = [];

    public function werkzeuge(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Werkzeug::class, 'Typ_ID');
    }
}
