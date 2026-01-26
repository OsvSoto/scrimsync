<?php
$current_uri = $_SERVER['REQUEST_URI'];
?>
<!-- Overlay for mobile -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden transition-opacity opacity-0"></div>

<aside id="sidebar" class="w-64 bg-surface border-r-2 border-primary flex flex-col h-screen fixed left-0 top-0 pt-20 z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-300">
  <div class="px-6 py-4">

    <nav class="space-y-2">
      <a href="#"
        class="flex items-center gap-3 px-4 py-3 text-sm font-bold transition-colors border-l-4 <?php echo (strpos($current_uri, 'index.php') !== false && strpos($current_uri, 'modules') === false) ? 'text-primary bg-zinc-100 border-primary' : 'text-secondary border-transparent hover:text-primary hover:bg-zinc-100 hover:border-primary'; ?>">
        <i data-lucide="home" class="w-4 h-4"></i>
        Dashboard
      </a>
    </nav>

    <nav class="space-y-2">
      <a href="#"
        class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-secondary hover:text-primary hover:bg-zinc-100 transition-colors border-l-4 border-transparent hover:border-primary">
        <i data-lucide="shield" class="w-4 h-4"></i>
        Mi Equipo
      </a>
    </nav>

    <nav class="space-y-2">
      <a href="#"
        class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-secondary hover:text-primary hover:bg-zinc-100 transition-colors border-l-4 border-transparent hover:border-primary">
        <i data-lucide="search" class="w-4 h-4"></i>
        Buscar Scrim
      </a>
    </nav>

    <nav class="space-y-2">
      <a href="#"
        class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-secondary hover:text-primary hover:bg-zinc-100 transition-colors border-l-4 border-transparent hover:border-primary">
        <i data-lucide="calendar" class="w-4 h-4"></i>
        Calendario
      </a>
    </nav>

    <nav class="space-y-2">
      <a href="#"
        class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-secondary hover:text-primary hover:bg-zinc-100 transition-colors border-l-4 border-transparent hover:border-primary">
        <i data-lucide="bell" class="w-4 h-4"></i>
        Notificaciones
      </a>
    </nav>

    <nav class="space-y-2">
      <a href="<?php echo BASE_URL; ?>modules/user/profile/index.php"
        class="flex items-center gap-3 px-4 py-3 text-sm font-bold transition-colors border-l-4 <?php echo (strpos($current_uri, 'modules/user/profile/') !== false) ? 'text-primary bg-zinc-100 border-primary' : 'text-secondary border-transparent hover:text-primary hover:bg-zinc-100 hover:border-primary'; ?>">
        <i data-lucide="user" class="w-4 h-4"></i>
        Mi Perfil
      </a>
    </nav>
  </div>
</aside>
