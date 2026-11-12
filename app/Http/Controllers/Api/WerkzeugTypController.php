<?php

namespace App\Http\Controllers\Api;

use App\Models\Werkzeug;
use App\Models\Werkzeugtyp;
use Illuminate\Http\Request;
use Throwable;

class WerkzeugTypController extends ApiController
{
    public function index()
    {
        return $this->ok(['data' => Werkzeugtyp::query()->select('Typ_ID', 'Art as Typ')->get()]);
    }

    public function store(Request $request)
    {
        $art = $this->str($request, 'art');
        if ($art === '') {
            return $this->fail('Parameter art fehlt.');
        }

        try {
            Werkzeugtyp::create(['Art' => $art]);
        } catch (Throwable $e) {
            report($e);
            return $this->fail('Art konnte nicht hinzugefügt werden.', 500);
        }

        return $this->ok(['message' => 'Art erfolgreich hinzugefügt']);
    }

    public function update(Request $request)
    {
        $typId = (int)$request->input('typ_id', 0);
        $art = $this->str($request, 'art');
        if ($typId <= 0 || $art === '') {
            return $this->fail('Parameter typ_id und art müssen gesetzt sein.');
        }

        $typ = Werkzeugtyp::find($typId);
        if (!$typ) {
            return $this->fail('Art konnte nicht aktualisiert werden.', 404);
        }

        $typ->update(['Art' => $art]);

        return $this->ok(['message' => 'Art erfolgreich aktualisiert']);
    }

    public function destroy(Request $request)
    {
        $art = $this->str($request, 'art');
        if ($art === '') {
            return $this->fail('Parameter art fehlt.');
        }

        $typ = Werkzeugtyp::where('Art', $art)->first();
        if (!$typ || Werkzeug::where('Typ_ID', $typ->Typ_ID)->exists()) {
            return $this->fail('Art konnte nicht gelöscht werden oder wird noch verwendet.', 409);
        }

        $typ->delete();

        return $this->ok(['message' => 'Art erfolgreich gelöscht']);
    }
}
