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
      // colores para usar en el sistema
      theme: {
        extend: {
          colors: {
            background: '#fafafa', // zinc-50
            surface: '#ffffff',
            border: '#e4e4e7', // zinc-200
            primary: '#09090b', // zinc-950
            'primary-hover': '#27272a', // zinc-800
            secondary: '#71717a', // zinc-500
            muted: '#a1a1aa', // zinc-400
            subtle: '#f4f4f5', // zinc-100
            'error-light': '#ffe4e6', // rose-100
            'error-border': '#f43f5e', // rose-500
            'error-text': '#be123c', // rose-700
            'success-light': '#d1fae5', // emerald-100
            'success-border': '#10b981', // emerald-500
            'success-text': '#047857', // emerald-700
          },
          boxShadow: {
            'hard': '4px 4px 0px 0px #09090b',
            'hard-sm': '4px 4px 0px 0px rgba(0,0,0,0.1)',
            'hard-error': '4px 4px 0px 0px rgba(244,63,94,0.2)',
            'hard-success': '4px 4px 0px 0px rgba(16,185,129,0.2)',
          },
          fontFamily: {
            sans: ['Inter', 'system-ui', 'sans-serif'],
          }
        }
      }
    }
  </script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="<?php echo $bodyClass ?? 'bg-background font-sans'; ?>">
