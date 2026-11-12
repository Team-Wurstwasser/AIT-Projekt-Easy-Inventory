<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abteilung extends Model
{
    protected $table = 'Abteilung';
    protected $primaryKey = 'Abteilung_ID';
    public $timestamps = false;
    protected $guarded = [];

    public function mitarbeiter(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Mitarbeiter::class, 'Abteilung_ID');
    }
}
