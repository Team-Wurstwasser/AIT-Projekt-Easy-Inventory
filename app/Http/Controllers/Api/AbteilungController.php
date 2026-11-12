<?php

namespace App\Http\Controllers\Api;

use App\Models\Abteilung;
use App\Models\Mitarbeiter;
use Illuminate\Http\Request;
use Throwable;

class AbteilungController extends ApiController
{
    public function index()
    {
        return $this->ok(['data' => Abteilung::query()->select('Abteilung_ID', 'Name')->get()]);
    }

    public function store(Request $request)
    {
        $name = $this->str($request, 'name');
        if ($name === '') {
            return $this->fail('Parameter name fehlt.');
        }

        try {
            Abteilung::create(['Name' => $name]);
        } catch (Throwable $e) {
            report($e);
            return $this->fail('Abteilung konnte nicht hinzugefügt werden.', 500);
        }

        return $this->ok(['message' => 'Abteilung erfolgreich hinzugefügt']);
    }

    public function update(Request $request)
    {
        $id = (int)$request->input('abteilung_id', 0);
        $name = $this->str($request, 'name');
        if ($id <= 0 || $name === '') {
            return $this->fail('Parameter abteilung_id und name müssen gesetzt sein.');
        }

        $abteilung = Abteilung::find($id);
        if (!$abteilung) {
            return $this->fail('Abteilung konnte nicht aktualisiert werden.', 404);
        }

        try {
            $abteilung->update(['Name' => $name]);
        } catch (Throwable $e) {
            report($e);
            return $this->fail('Abteilung konnte nicht aktualisiert werden.', 500);
        }

        return $this->ok(['message' => 'Abteilung erfolgreich aktualisiert']);
    }

    public function destroy(Request $request)
    {
        $id = (int)$request->input('abteilung_id', 0);
        if ($id <= 0) {
            return $this->fail('Parameter abteilung_id fehlt.');
        }

        if (Mitarbeiter::where('Abteilung_ID', $id)->exists() || !Abteilung::whereKey($id)->delete()) {
            return $this->fail('Abteilung konnte nicht gelöscht werden oder wird noch verwendet.', 409);
        }

        return $this->ok(['message' => 'Abteilung erfolgreich gelöscht']);
    }
}
