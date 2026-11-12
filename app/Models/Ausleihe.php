<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Ausleihe extends Model
{
    protected $table = 'Ausleihe';
    protected $primaryKey = 'Ausleih_ID';
    public $timestamps = false;
    protected $guarded = [];

    public function mitarbeiter(): BelongsTo
    {
        return $this->belongsTo(Mitarbeiter::class, 'Mitarbeiter_ID');
    }

    public function werkzeug(): BelongsTo
    {
        return $this->belongsTo(Werkzeug::class, 'Barcode');
    }

    private const FAELLIG = 'DATE_ADD(a.Ausleihdatum, INTERVAL a.Ausleihdauer DAY)';

    private static function basis(): Builder
    {
        return DB::table('Ausleihe as a')
            ->join('Werkzeuge as w', 'a.Barcode', '=', 'w.Barcode')
            ->join('Mitarbeiter as m', 'a.Mitarbeiter_ID', '=', 'm.Mitarbeiter_ID');
    }

    public static function istAusgeliehen(string $barcode): bool
    {
        return static::where('Barcode', $barcode)->whereNull('Rückgabedatum')->exists();
    }

    /** Aktuell ausgeliehene Werkzeuge. */
    public static function offene(): Collection
    {
        return self::basis()
            ->whereNull('a.Rückgabedatum')
            ->selectRaw('a.Ausleih_ID as Ausleih_ID, a.Ausleihdatum as Ausleihdatum, ' . self::FAELLIG . ' as Fälligkeitsdatum, a.Barcode as Barcode, w.Bezeichnung as Bezeichnung, m.Vorname as Vorname, m.Nachname as Nachname, m.Email as Email')
            ->get();
    }

    public static function historie(): Collection
    {
        return self::basis()
            ->selectRaw('a.Ausleih_ID as Ausleih_ID, a.Ausleihdatum as Ausleihdatum, ' . self::FAELLIG . ' as Fälligkeitsdatum, a.Rückgabedatum as Rückgabedatum, a.Barcode as Barcode, w.Bezeichnung as Bezeichnung, m.Vorname as Vorname, m.Nachname as Nachname, m.Email as Email, a.ZustandBeiRückgabe as ZustandBeiRückgabe')
            ->get();
    }

    public static function faelligIn48h(): Collection
    {
        return self::basis()
            ->whereNull('a.Rückgabedatum')
            ->where('a.EmailVersendet', 0)
            ->whereRaw(self::FAELLIG . ' <= ?', [now()->addHours(48)->toDateTimeString()])
            ->whereRaw(self::FAELLIG . ' >= ?', [now()->toDateTimeString()])
            ->selectRaw('a.Ausleih_ID as Ausleih_ID, ' . self::FAELLIG . ' as Fälligkeitsdatum, w.Barcode as Barcode, w.Bezeichnung as Bezeichnung, m.Vorname as Vorname, m.Nachname as Nachname, m.Email as Email')
            ->get();
    }
}
