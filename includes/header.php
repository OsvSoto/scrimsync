<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Acceso - ScrimSync</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              background: '#fafafa',
              surface: '#ffffff',
              border: '#e4e4e7',
              primary: '#09090b',
              secondary: '#71717a',
            },
            fontFamily: {
              sans: ['Inter', 'system-ui', 'sans-serif'],
            }
          }
        }
      }
    </script>
</head>
<body class="<?php echo $bodyClass ?? 'bg-background font-sans'; ?>">

