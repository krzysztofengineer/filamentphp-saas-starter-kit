<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline — {{ config('app.name') }}</title>
    @include('partials.pwa-head')
    <style>
        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
            background: #FDFDFC;
            color: #1b1b18;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .card {
            max-width: 28rem;
            text-align: center;
        }
        h1 {
            font-size: 1.5rem;
            margin: 0 0 .5rem;
        }
        p {
            margin: 0 0 1rem;
            color: #52525b;
        }
        button {
            background: #2563EB;
            color: #fff;
            border: 0;
            border-radius: .5rem;
            padding: .625rem 1.25rem;
            font-size: 1rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Offline</h1>
        <p>You are offline. Check your internet connection and try again.</p>
        <button type="button" onclick="location.reload()">Try again</button>
    </div>
</body>
</html>
