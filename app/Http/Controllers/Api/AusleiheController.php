<?php

namespace App\Http\Controllers\Api;

use App\Models\Ausleihe;
use App\Models\Werkzeug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class AusleiheController extends ApiController
{
    private const STATUS_VERFUEGBAR = 1;
    private const STATUS_AUSGELIEHEN = 2;

    public function ausgeliehen()
    {
        return $this->ok(['data' => Ausleihe::offene()]);
    }

    public function historie()
    {
        return $this->ok(['data' => Ausleihe::historie()]);
    }

    public function ausleihen(Request $request)
    {
        $barcode = $this->str($request, 'barcode');
        $mitarbeiterId = (int)$request->input('mitarbeiter_id', 0);
        $dauer = (int)$request->input('ausleihdauer', 0);

        if ($barcode === '' || $mitarbeiterId <= 0 || $dauer <= 0) {
            return $this->fail('Parameter barcode, mitarbeiter_id oder ausleihdauer fehlen.');
        }
        if (Ausleihe::istAusgeliehen($barcode)) {
            return $this->fail('Werkzeug ist bereits ausgeliehen.', 409);
        }

        try {
            DB::transaction(function () use ($barcode, $mitarbeiterId, $dauer) {
                Ausleihe::create([
                    'Ausleihdatum' => now()->toDateString(),
                    'Ausleihdauer' => $dauer,
                    'Mitarbeiter_ID' => $mitarbeiterId,
                    'Barcode' => $barcode,
                    'EmailVersendet' => 0,
                ]);
                Werkzeug::whereKey($barcode)->update(['Status_ID' => self::STATUS_AUSGELIEHEN]);
            });
        } catch (Throwable $e) {
            report($e);
            return $this->fail('Fehler beim Ausleihen', 500);
        }

        return $this->ok(['message' => 'Erfolgreich ausgeliehen']);
    }

    public function abgeben(Request $request)
    {
        $barcode = $this->str($request, 'barcode');
        $zustand = $this->str($request, 'zustand', 'OK');

        if ($barcode === '') {
            return $this->fail('Parameter barcode fehlt.');
        }
        if (!Ausleihe::istAusgeliehen($barcode)) {
            return $this->fail('Werkzeug ist nicht ausgeliehen.', 409);
        }

        try {
            DB::transaction(function () use ($barcode, $zustand) {
                Ausleihe::where('Barcode', $barcode)->whereNull('Rückgabedatum')->update([
                    'ZustandBeiRückgabe' => $zustand,
                    'Rückgabedatum' => now()->toDateString(),
                ]);
                Werkzeug::whereKey($barcode)->update(['Status_ID' => self::STATUS_VERFUEGBAR]);
            });
        } catch (Throwable $e) {
            report($e);
            return $this->fail('Fehler bei der Rückgabe in der Datenbank.', 500);
        }

        return $this->ok(['message' => 'Erfolgreich abgegeben']);
    }

    public function verlaengern(Request $request)
    {
        $ausleihId = (int)$request->input('ausleih_id', 0);
        $zusatzTage = (int)$request->input('zusatz_tage', 0);

        if ($ausleihId <= 0 || $zusatzTage <= 0) {
            return $this->fail('Parameter ausleih_id und zusatz_tage müssen gesetzt sein und größer als 0 sein.');
        }

        $geaendert = Ausleihe::whereKey($ausleihId)->whereNull('Rückgabedatum')->update([
            'Ausleihdauer' => DB::raw('Ausleihdauer + ' . $zusatzTage),
            'EmailVersendet' => 0,
        ]);

        if ($geaendert === 0) {
            return $this->fail('Ausleihe konnte nicht verlängert werden.', 404);
        }

        return $this->ok(['message' => 'Ausleihe erfolgreich verlängert']);
    }
}
