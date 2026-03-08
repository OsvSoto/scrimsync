<?php
$current_uri = $_SERVER['REQUEST_URI'];
?>
<nav class="fixed top-0 left-0 w-full h-10 bg-surface border-b-2 border-primary z-50">
    <div class="max-w-6xl mx-auto h-full flex items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-1 sm:gap-3">
            <div class="flex items-center gap-2 mr-2">
                <a href="/index.php" class="cursor-pointer flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-primary tracking-tighter transition-colors hover:text-scrimsync"> SCRIMSYNC </h1>
                    <span class="bg-primary text-white text-[10px] px-2 py-0.5 font-bold uppercase tracking-widest hidden sm:inline-block">Admin</span>
                </a>
            </div>
            <a href="/modules/admin/dashboard.php"
                title="Dashboard"
                class="flex items-center gap-1 px-2 py-1 text-sm transition-colors
                <?php echo (strpos($current_uri, 'modules/admin/dashboard') !== false) ? 'text-primary font-bold border-primary' : 'text-secondary border-transparent hover:text-primary hover:border-primary'; ?>">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                <span class="hidden md:inline">Dashboard</span>
            </a>
            <a href="/modules/admin/mantencion/index.php"
                title="Tablas Básicas"
                class="flex items-center gap-1 px-2 py-1 text-sm transition-colors
                <?php echo (strpos($current_uri, 'modules/admin/mantencion') !== false) ? 'text-primary font-bold border-primary' : 'text-secondary border-transparent hover:text-primary hover:border-primary'; ?>">
                <i data-lucide="database" class="w-4 h-4"></i>
                <span class="hidden md:inline">Tablas Básicas</span>
            </a>
            <a href="/modules/admin/usuarios/index.php"
                title="Gestión de Usuarios"
                class="flex items-center gap-1 px-2 py-1 text-sm transition-colors
                <?php echo (strpos($current_uri, 'modules/admin/usuarios') !== false) ? 'text-primary font-bold border-primary' : 'text-secondary border-transparent hover:text-primary hover:border-primary'; ?>">
                <i data-lucide="users" class="w-4 h-4"></i>
                <span class="hidden md:inline">Gestión de Usuarios</span>
            </a>
            <a href="/modules/admin/backup/index.php"
                title="Respaldo BD"
                class="flex items-center gap-1 px-2 py-1 text-sm transition-colors
                <?php echo (strpos($current_uri, 'modules/admin/backup') !== false) ? 'text-primary font-bold border-primary' : 'text-secondary border-transparent hover:text-primary hover:border-primary'; ?>">
                <i data-lucide="database" class="w-4 h-4"></i>
                <span class="hidden md:inline">Respaldo BD</span>
            </a>
        </div>
        <div class="flex items-center gap-1 sm:gap-2">
            <a href="/modules/user/profile/index.php"
                title="Mi Perfil"
                class="flex items-center gap-1 px-2 py-1 text-sm transition-colors <?php echo (strpos($current_uri, 'modules/user/profile/') !== false) ? 'text-primary font-bold border-primary' : 'text-secondary border-transparent hover:text-primary hover:border-primary'; ?>">
                <div class="relative">
                    <i data-lucide="user" class="w-4 h-4"></i>
                </div>
            </a>

            <a href="/modules/auth/logout.php"
                title="Salir"
                class="flex items-center gap-1 px-2 py-1 text-sm transition-colors text-secondary hover:text-error-text">
                <i data-lucide="log-out" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</nav>
