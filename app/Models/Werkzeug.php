<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Werkzeug extends Model
{
    protected $table = 'Werkzeuge';
    protected $primaryKey = 'Barcode';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'Status_ID');
    }

    public function typ(): BelongsTo
    {
        return $this->belongsTo(Werkzeugtyp::class, 'Typ_ID');
    }

    public function ausleihen(): HasMany
    {
        return $this->hasMany(Ausleihe::class, 'Barcode');
    }

    public static function alleMitDetails()
    {
        return \Illuminate\Support\Facades\DB::table('Werkzeuge as w')
            ->join('Status as s', 'w.Status_ID', '=', 's.Status_ID')
            ->join('Werkzeugtyp as t', 'w.Typ_ID', '=', 't.Typ_ID')
            ->select(
                'w.Barcode as Barcode',
                'w.Bezeichnung as Bezeichnung',
                't.Art as Typ',
                'w.Anschaffungsdatum as Anschaffungsdatum',
                's.Bezeichnung as Status',
                's.Status_ID as Status_ID',
                't.Typ_ID as Typ_ID'
            )
            ->get();
    }
}
