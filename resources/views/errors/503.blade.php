<!DOCTYPE html>
{{-- Bewusst ohne Vite: Während des Deploys werden die Assets gerade neu gebaut. --}}
<html lang="de">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gleich wieder da - Ewige Liste</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            box-sizing: border-box;
            font-family: system-ui, sans-serif;
            text-align: center;
            color: #1f2937;
            background: #fff;
        }

        h1 {
            font-size: 1.5rem;
            color: #054b6d;
        }

        @media (prefers-color-scheme: dark) {
            body {
                color: #e5e7eb;
                background: #111827;
            }

            h1 {
                color: #fff;
            }
        }
    </style>
</head>
<body>
    <main>
        <h1>Ewige Liste</h1>
        <p>Tolle neue Änderungen! Die Seite ist gleich wieder da.</p>
    </main>
</body>
</html>
