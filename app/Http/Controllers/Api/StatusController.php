<?php

namespace App\Http\Controllers\Api;

use App\Models\Status;
use App\Models\Werkzeug;
use Illuminate\Http\Request;
use Throwable;

class StatusController extends ApiController
{
    public function index()
    {
        return $this->ok(['data' => Status::query()->select('Status_ID', 'Bezeichnung')->get()]);
    }

    public function store(Request $request)
    {
        $bezeichnung = $this->str($request, 'bezeichnung');
        if ($bezeichnung === '') {
            return $this->fail('Parameter bezeichnung fehlt.');
        }

        try {
            Status::create(['Bezeichnung' => $bezeichnung]);
        } catch (Throwable $e) {
            report($e);
            return $this->fail('Status konnte nicht hinzugefügt werden.', 500);
        }

        return $this->ok(['message' => 'Status erfolgreich hinzugefügt']);
    }

    public function update(Request $request)
    {
        $statusId = (int)$request->input('status_id', 0);
        $bezeichnung = $this->str($request, 'bezeichnung');
        if ($statusId <= 0 || $bezeichnung === '') {
            return $this->fail('Parameter status_id und bezeichnung müssen gesetzt sein.');
        }

        $status = Status::find($statusId);
        if (!$status) {
            return $this->fail('Status konnte nicht aktualisiert werden.', 404);
        }

        $status->update(['Bezeichnung' => $bezeichnung]);

        return $this->ok(['message' => 'Status erfolgreich aktualisiert']);
    }

    public function destroy(Request $request)
    {
        $statusId = (int)$request->input('status_id', 0);
        if ($statusId <= 0) {
            return $this->fail('Parameter status_id fehlt.');
        }

        if (Werkzeug::where('Status_ID', $statusId)->exists() || !Status::whereKey($statusId)->delete()) {
            return $this->fail('Status konnte nicht gelöscht werden oder wird noch verwendet.', 409);
        }

        return $this->ok(['message' => 'Status erfolgreich gelöscht']);
    }
}
