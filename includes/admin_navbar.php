<nav class="fixed top-0 left-0 w-full h-16 bg-surface border-b-2 border-primary z-50 flex items-center justify-between px-4 sm:px-6 bg-white">
  <div class="flex items-center gap-3">
    <button id="mobile-menu-btn" class="md:hidden p-1 hover:bg-zinc-100 rounded text-primary">
      <i data-lucide="menu" class="w-6 h-6"></i>
    </button>

    <div class="flex items-center gap-2">
      <h1 class="text-xl sm:text-2xl font-black text-primary tracking-tighter">SCRIMSYNC</h1>
      <span class="bg-primary text-white text-[10px] px-2 py-0.5 font-bold uppercase tracking-widest hidden sm:inline-block">Admin</span>
    </div>
  </div>
  <div class="flex items-center gap-4">
    <span class="text-xs sm:text-sm font-bold text-secondary hidden sm:inline-block">
      Hola, <?php echo isset($_SESSION['alias']) ? htmlspecialchars($_SESSION['alias']) : 'Admin'; ?>
    </span>
    <a href="/modules/auth/logout.php"
      class="text-xs font-black text-secondary hover:text-rose-600 transition-colors uppercase tracking-widest flex items-center gap-2">
      <i data-lucide="log-out" class="w-4 h-4"></i>
      <span class="hidden md:inline">SALIR</span>
    </a>
  </div>
</nav>
