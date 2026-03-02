<?php
$current_uri = $_SERVER['REQUEST_URI'];
?>
<nav class="fixed top-0 left-0 w-full h-10 bg-surface border-b-2 border-primary z-50 flex items-center justify-between px-4 sm:px-6">
  <div class="flex items-center gap-3">
    <!-- <button id="mobile-menu-btn" class="md:hidden p-1 hover:bg-zinc-100 rounded text-primary">
      <i data-lucide="menu" class="w-6 h-6"></i>
    </button> -->

    <div class="flex items-center gap-2">
      <h1 class="text-xl sm:text-2xl font-black text-primary tracking-tighter">SCRIMSYNC</h1>
      <span class="bg-primary text-white text-[10px] px-2 py-0.5 font-bold uppercase tracking-widest hidden sm:inline-block">Admin</span>
    </div>
    <a href="/modules/admin/dashboard.php"
      class="flex items-center gap-1 px-2 py-2 text-sm transition-colors <?php echo (strpos($current_uri, 'modules/admin/dashboard') !== false) ? 'text-primary font-bold border-primary' : 'text-secondary border-transparent hover:text-primary hover:border-primary'; ?>">
      <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
      <span class="hidden md:inline">Dashboard</span>
    </a>
    <a href="/modules/admin/mantencion/index.php"
      class="flex items-center gap-1 px-2 py-2 text-sm transition-colors <?php echo (strpos($current_uri, 'modules/admin/mantencion') !== false) ? 'text-primary font-bold border-primary' : 'text-secondary border-transparent hover:text-primary hover:border-primary'; ?>">
      <i data-lucide="database" class="w-4 h-4"></i>
      <span class="hidden md:inline">Tablas Básicas</span>
    </a>
    <a href="/modules/admin/usuarios/index.php"
      class="flex items-center gap-1 px-2 py-2 text-sm transition-colors <?php echo (strpos($current_uri, 'modules/admin/usuarios') !== false) ? 'text-primary font-bold border-primary' : 'text-secondary border-transparent hover:text-primary hover:border-primary'; ?>">
      <i data-lucide="users" class="w-4 h-4"></i>
      <span class="hidden md:inline">Gestión de Usuarios</span>
    </a>
  </div>
  <div class="dropdown dropdown-end">
    <div tabindex="0" role="button"
         class="bg-surface text-secondary hover:text-primary shadow-none rounded-none" title="Perfil">
      <i data-lucide="user" class="w-4 h-4"></i>
    </div>
    <ul tabindex="0"
        class="dropdown-content z-[1] menu p-2 shadow-hard bg-surface border-2 border-primary w-max min-w-[160px] rounded-none mt-1">
      <li>
        <a href="/modules/user/profile/index.php"
          class="flex items-center gap-1 px-2 py-2 text-sm transition-colors <?php echo (strpos($current_uri, 'modules/user/profile/') !== false) ? 'text-primary font-bold border-primary' : 'text-secondary border-transparent hover:text-primary hover:border-primary'; ?>">
          <i data-lucide="user" class="w-4 h-4"></i>
          <span>Mi Perfil</span>
        </a>
        <a href="/modules/auth/logout.php"
          class="flex items-center gap-1 px-2 py-2 text-sm transition-colors text-secondary hover:text-error-text">
          <i data-lucide="log-out" class="w-4 h-4"></i>
          <span>Salir</span>
        </a>
    </ul>
  </div>
</nav>
