<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Interner Serverfehler</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
</head>
<body style="min-height: 100vh; display: flex; flex-direction: column;">
    <header class="header">
        <img class="header_logo" src="{{ asset('img/logo.png') }}" alt="Easy Inventory Logo">
    </header>

    <main style="flex: 1; display: flex; align-items: center; justify-content: center;">
        <div class="icon-header" style="width: 100%; justify-content: center;">
            <div style="width: 100%; text-align: center;">
                <h1 class="HeaderIconÜbersicht">Interner Serverfehler</h1>
                <h1 style="font-size: 120px; line-height: 1; margin: 10px 0 20px; color: #000080;">500</h1>
                <p>Upps, auf unserem Server ist leider etwas schiefgelaufen.</p>
                <p>Wir arbeiten bereits daran, das Problem zu beheben. Bitte versuche es später noch einmal.</p>
            </div>
        </div>
    </main>
</body>
</html>
