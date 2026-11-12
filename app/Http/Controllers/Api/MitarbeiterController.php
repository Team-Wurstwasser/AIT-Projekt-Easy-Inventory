<?php

namespace App\Http\Controllers\Api;

use App\Models\Mitarbeiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class MitarbeiterController extends ApiController
{
    public function index()
    {
        $data = DB::table('Mitarbeiter as m')
            ->join('Abteilung as ab', 'm.Abteilung_ID', '=', 'ab.Abteilung_ID')
            ->select('m.Mitarbeiter_ID as Mitarbeiter_ID', 'm.Vorname as Vorname', 'm.Nachname as Nachname', 'm.Email as Email', 'ab.Name as Abteilung', 'ab.Abteilung_ID as Abteilung_ID')
            ->get();

        return $this->ok(['data' => $data]);
    }

    public function store(Request $request)
    {
        $d = $this->fields($request);
        if (in_array('', [$d['Vorname'], $d['Nachname'], $d['Email']], true) || $d['Abteilung_ID'] <= 0) {
            return $this->fail('Parameter vorname, nachname, email und abteilung_id fehlen.');
        }
        if (!filter_var($d['Email'], FILTER_VALIDATE_EMAIL)) {
            return $this->fail('Die angegebene E-Mail-Adresse ist ungültig.');
        }

        try {
            Mitarbeiter::create($d);
        } catch (Throwable $e) {
            report($e);
            return $this->fail('Mitarbeiter konnte nicht hinzugefügt werden.', 500);
        }

        return $this->ok(['message' => 'Mitarbeiter erfolgreich hinzugefügt']);
    }

    public function update(Request $request)
    {
        $id = (int)$request->input('mitarbeiter_id', 0);
        $d = $this->fields($request);
        if ($id <= 0 || in_array('', [$d['Vorname'], $d['Nachname'], $d['Email']], true) || $d['Abteilung_ID'] <= 0) {
            return $this->fail('Parameter mitarbeiter_id, vorname, nachname, email und abteilung_id müssen gesetzt sein.');
        }
        if (!filter_var($d['Email'], FILTER_VALIDATE_EMAIL)) {
            return $this->fail('Die angegebene E-Mail-Adresse ist ungültig.');
        }

        $mitarbeiter = Mitarbeiter::find($id);
        if (!$mitarbeiter) {
            return $this->fail('Mitarbeiter konnte nicht aktualisiert werden.', 404);
        }

        try {
            $mitarbeiter->update($d);
        } catch (Throwable $e) {
            report($e);
            return $this->fail('Mitarbeiter konnte nicht aktualisiert werden.', 500);
        }

        return $this->ok(['message' => 'Mitarbeiter erfolgreich aktualisiert']);
    }

    public function destroy(Request $request)
    {
        $id = (int)$request->input('mitarbeiter_id', 0);
        if ($id <= 0) {
            return $this->fail('Parameter mitarbeiter_id fehlt.');
        }

        try {
            DB::transaction(function () use ($id) {
                DB::table('Ausleihe')->where('Mitarbeiter_ID', $id)->delete();
                Mitarbeiter::whereKey($id)->delete();
            });
        } catch (Throwable $e) {
            report($e);
            return $this->fail('Mitarbeiter konnte nicht gelöscht werden oder wird noch verwendet.', 409);
        }

        return $this->ok(['message' => 'Mitarbeiter erfolgreich gelöscht']);
    }

    private function fields(Request $request): array
    {
        return [
            'Vorname' => $this->str($request, 'vorname'),
            'Nachname' => $this->str($request, 'nachname'),
            'Email' => $this->str($request, 'email'),
            'Abteilung_ID' => (int)$request->input('abteilung_id', 0),
        ];
    }
}
