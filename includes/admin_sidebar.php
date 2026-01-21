<aside class="w-64 bg-surface border-r-2 border-primary hidden md:flex flex-col h-screen fixed left-0 top-0 pt-20">
    <div class="px-6 py-4">
        
        <h3 class="text-[10px] font-black uppercase text-secondary tracking-widest mb-4">
            Principal
        </h3>
        <nav class="space-y-2">
            <a href="<?php echo BASE_URL; ?>modules/admin/dashboard.php" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-primary hover:bg-zinc-100 transition-colors border-l-4 border-transparent hover:border-primary">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                Dashboard
            </a>
        </nav>

        <h3 class="text-[10px] font-black uppercase text-secondary tracking-widest mb-4 mt-8">
            Módulos de Gestión
        </h3>
        <nav class="space-y-2">
            
            <a href="<?php echo BASE_URL; ?>modules/admin/mantencion/index.php" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-secondary hover:text-primary hover:bg-zinc-100 transition-colors border-l-4 border-transparent hover:border-primary">
                <i data-lucide="database" class="w-4 h-4"></i>
                Tablas Básicas
            </a>

        </nav>

        <h3 class="text-[10px] font-black uppercase text-secondary tracking-widest mb-4 mt-8">
            Sistema
        </h3>
        <nav class="space-y-2">
            <a href="<?php echo BASE_URL; ?>modules/auth/logout.php" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                Cerrar Sesión
            </a>
        </nav>
    </div>
</aside>