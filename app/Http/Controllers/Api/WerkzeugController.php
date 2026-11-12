<?php

namespace App\Http\Controllers\Api;

use App\Models\Werkzeug;
use App\Helpers\Ean13;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class WerkzeugController extends ApiController
{
    public function index()
    {
        return $this->ok(['data' => Werkzeug::alleMitDetails()]);
    }

    public function store(Request $request)
    {
        $barcode = $this->str($request, 'barcode');
        $bezeichnung = $this->str($request, 'bezeichnung');
        $typId = (int)$request->input('typ_id', 0);
        $datum = $this->str($request, 'anschaffungsdatum');

        if ($barcode === '' || $bezeichnung === '' || $typId <= 0) {
            return $this->fail('Parameter barcode, bezeichnung und typ_id fehlen.');
        }
        if (!Ean13::isValid($barcode)) {
            return $this->fail('Barcode muss ein gültiger EAN-13-Code sein.');
        }
        if (Werkzeug::whereKey($barcode)->exists()) {
            return $this->fail('Barcode existiert bereits.', 409);
        }

        try {
            Werkzeug::create([
                'Barcode' => $barcode,
                'Bezeichnung' => $bezeichnung,
                'Typ_ID' => $typId,
                'Anschaffungsdatum' => $datum !== '' ? $datum : now()->toDateString(),
                'Status_ID' => 1,
            ]);
        } catch (Throwable $e) {
            report($e);
            return $this->fail('Werkzeug konnte nicht hinzugefügt werden.', 500);
        }

        return $this->ok(['message' => 'Werkzeug erfolgreich hinzugefügt']);
    }

    public function update(Request $request)
    {
        $barcode = $this->str($request, 'barcode');
        $bezeichnung = $this->str($request, 'bezeichnung');
        $typId = (int)$request->input('typ_id', 0);
        $statusId = (int)$request->input('status_id', 0);
        $datum = $this->str($request, 'anschaffungsdatum');

        if ($barcode === '' || $bezeichnung === '' || $typId <= 0 || $statusId <= 0) {
            return $this->fail('Parameter barcode, bezeichnung, typ_id und status_id fehlen.');
        }

        $werkzeug = Werkzeug::find($barcode);
        if (!$werkzeug) {
            return $this->fail('Werkzeug nicht gefunden.', 404);
        }

        $werkzeug->Bezeichnung = $bezeichnung;
        $werkzeug->Typ_ID = $typId;
        $werkzeug->Status_ID = $statusId;
        if ($datum !== '') {
            $werkzeug->Anschaffungsdatum = $datum;
        }

        try {
            $werkzeug->save();
        } catch (Throwable $e) {
            report($e);
            return $this->fail('Werkzeug konnte nicht aktualisiert werden.', 500);
        }

        return $this->ok(['message' => 'Werkzeug erfolgreich aktualisiert']);
    }

    public function destroy(Request $request)
    {
        $barcode = $this->str($request, 'barcode');
        if ($barcode === '') {
            return $this->fail('Parameter barcode fehlt.');
        }

        try {
            DB::transaction(function () use ($barcode) {
                DB::table('Ausleihe')->where('Barcode', $barcode)->delete();
                Werkzeug::whereKey($barcode)->delete();
            });
        } catch (Throwable $e) {
            report($e);
            return $this->fail('Werkzeug konnte nicht gelöscht werden.', 500);
        }

        return $this->ok(['message' => 'Werkzeug erfolgreich gelöscht']);
    }
}
