<nav class="fixed top-0 left-0 w-full h-16 bg-surface border-b-2 border-primary z-50 flex items-center justify-between px-4 sm:px-6 bg-white">
  <div class="flex items-center gap-3">
    <!-- Mobile Menu Button -->
    <button id="mobile-menu-btn" class="md:hidden p-1 hover:bg-zinc-100 rounded text-primary">
      <i data-lucide="menu" class="w-6 h-6"></i>
    </button>

    <a href="../../../index.php" class="flex items-center gap-2">
      <span class="font-black text-xl sm:text-2xl tracking-tighter text-primary">SCRIMSYNC</span>
    </a>
  </div>
  <div class="flex items-center gap-6">
    <a href="../../../index.php" class="text-xs font-black text-secondary hover:text-primary transition-colors uppercase tracking-widest flex items-center gap-2">
      <i data-lucide="home" class="w-4 h-4"></i>
      <span class="hidden md:inline">Inicio</span>
    </a>
    <a href="../../../modules/auth/logout.php"
      class="text-xs font-black text-secondary hover:text-rose-600 transition-colors uppercase tracking-widest flex items-center gap-2">
      <i data-lucide="log-out" class="w-4 h-4"></i>
      <span class="hidden md:inline">SALIR</span>
    </a>
  </div>
</nav>
