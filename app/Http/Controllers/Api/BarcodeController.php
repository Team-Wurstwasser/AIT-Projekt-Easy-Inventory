<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Ean13;
use Illuminate\Http\Request;
use Picqer\Barcode\Renderers\PngRenderer;
use Picqer\Barcode\Types\TypeEan13;

class BarcodeController extends ApiController
{
    public function __invoke(Request $request)
    {
        $code = trim((string)$request->query('code', ''));

        if ($code === '') {
            return $this->ok(['barcode' => Ean13::generate()]);
        }

        if (!Ean13::isValid($code)) {
            return $this->fail('Barcode muss ein gültiger EAN-13-Code sein.');
        }

        $barcode = (new TypeEan13())->getBarcode($code);
        $png = (new PngRenderer())->render($barcode, max($barcode->getWidth() * 2, 100), 50);

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
