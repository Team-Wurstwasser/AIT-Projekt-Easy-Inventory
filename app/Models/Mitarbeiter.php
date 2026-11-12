<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mitarbeiter extends Model
{
    protected $table = 'Mitarbeiter';
    protected $primaryKey = 'Mitarbeiter_ID';
    public $timestamps = false;
    protected $guarded = [];

    public function abteilung(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Abteilung::class, 'Abteilung_ID');
    }

    public function ausleihen(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ausleihe::class, 'Mitarbeiter_ID');
    }
}
