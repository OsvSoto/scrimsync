<?php
// modules/admin/dashboard.php
session_start();
require_once '../../config/db.php';

// 1. Seguridad: Verificar Admin
if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] != 0) {
    header("Location: " . BASE_URL . "modules/auth/login.php");
    exit;
}

include '../../includes/header.php';
?>

<?php include '../../includes/admin_navbar.php'; ?>

<div class="min-h-screen pt-16 bg-background">

    <?php include '../../includes/admin_sidebar.php'; ?>

    <main class="md:ml-64 p-8">

        <header class="mb-10">
            <h2 class="text-3xl font-black text-primary uppercase tracking-tight mb-2">Panel de Control</h2>
            <p class="text-secondary text-sm">Resumen general del sistema.</p>
        </header>

        <h3 class="text-xl font-black text-primary uppercase tracking-tight mb-6">Módulos del Sistema</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <a href="mantencion/index.php" class="group flex flex-col p-8 bg-surface border-2 border-primary hover:bg-primary transition-all shadow-[4px_4px_0px_0px_rgba(9,9,11,1)] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px]">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-zinc-100 group-hover:bg-zinc-800 transition-colors">
                        <i data-lucide="database" class="w-8 h-8 text-primary group-hover:text-white"></i>
                    </div>
                    <i data-lucide="arrow-right" class="w-5 h-5 text-zinc-300 group-hover:text-white opacity-0 group-hover:opacity-100 transition-all"></i>
                </div>

                <h4 class="text-lg font-black uppercase text-primary group-hover:text-white mb-2">
                    Tablas Básicas
                </h4>
                <p class="text-xs text-secondary group-hover:text-zinc-400 font-medium">
                    Gestión centralizada de Juegos, Roles y Géneros.
                </p>
            </a>

        </div>

    </main>
</div>

<?php include '../../includes/footer.php'; ?>
