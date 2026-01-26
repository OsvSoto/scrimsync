<?php
$current_uri = $_SERVER['REQUEST_URI'];
?>
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden transition-opacity opacity-0"></div>
<aside id="sidebar" class="w-64 bg-surface border-r-2 border-primary flex flex-col h-screen fixed left-0 top-0 pt-20 z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-300">
    <div class="px-6 py-4">

        <h3 class="text-[10px] font-black uppercase text-secondary tracking-widest mb-4">
            Principal
        </h3>
        <nav class="space-y-2">
            <a href="<?php echo BASE_URL; ?>modules/admin/dashboard.php"
               class="flex items-center gap-3 px-4 py-3 text-sm font-bold transition-colors border-l-4 <?php echo (strpos($current_uri, 'modules/admin/dashboard.php') !== false) ? 'text-primary bg-zinc-100 border-primary' : 'text-secondary border-transparent hover:text-primary hover:bg-zinc-100 hover:border-primary'; ?>">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                Dashboard
            </a>
        </nav>

        <h3 class="text-[10px] font-black uppercase text-secondary tracking-widest mb-4 mt-8">
            Módulos de Gestión
        </h3>
        <nav class="space-y-2">

            <a href="<?php echo BASE_URL; ?>modules/admin/mantencion/index.php"
               class="flex items-center gap-3 px-4 py-3 text-sm font-bold transition-colors border-l-4 <?php echo (strpos($current_uri, 'modules/admin/mantencion/') !== false) ? 'text-primary bg-zinc-100 border-primary' : 'text-secondary border-transparent hover:text-primary hover:bg-zinc-100 hover:border-primary'; ?>">
                <i data-lucide="database" class="w-4 h-4"></i>
                Tablas Básicas
            </a>

            <a href="<?php echo BASE_URL; ?>modules/admin/usuarios/index.php"
               class="flex items-center gap-3 px-4 py-3 text-sm font-bold transition-colors border-l-4 <?php echo (strpos($current_uri, 'modules/admin/usuarios/') !== false) ? 'text-primary bg-zinc-100 border-primary' : 'text-secondary border-transparent hover:text-primary hover:bg-zinc-100 hover:border-primary'; ?>">
                <i data-lucide="users" class="w-4 h-4"></i>
                Gestión de Usuarios
            </a>

        </nav>

        <h3 class="text-[10px] font-black uppercase text-secondary tracking-widest mb-4 mt-8">
            Sistema
        </h3>
        <nav class="space-y-2">
            <a href="<?php echo BASE_URL; ?>modules/user/profile/index.php"
               class="flex items-center gap-3 px-4 py-3 text-sm font-bold transition-colors border-l-4 <?php echo (strpos($current_uri, 'modules/user/profile/') !== false) ? 'text-primary bg-zinc-100 border-primary' : 'text-secondary border-transparent hover:text-primary hover:bg-zinc-100 hover:border-primary'; ?>">
                <i data-lucide="user" class="w-4 h-4"></i>
                Mi Perfil
            </a>
        </nav>
    </div>
</aside>
