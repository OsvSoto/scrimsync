<?php
// modules/admin/dashboard.php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['tipo'] != 0) {
  header("Location: ../../../modules/auth/login.php");
  exit;
}

include '../../includes/header.php';
?>

<?php include '../../includes/admin_navbar.php'; ?>

<div class="min-h-screen bg-background">
  <main class="flex-1 w-full pt-16 pb-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

      <header class="mb-10">
        <h2 class="text-3xl font-black text-primary uppercase tracking-tight mb-2">Panel de Control</h2>
        <p class="text-secondary text-sm">Resumen general del sistema.</p>
      </header>

      <h3 class="text-xl font-black text-primary uppercase tracking-tight mb-6">Módulos del Sistema</h3>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <a href="mantencion/index.php" class="group flex flex-col p-8 bg-surface border-2 border-primary transition-all shadow-hard-sm hover:shadow-hard">
          <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-subtle">
              <i data-lucide="database" class="w-8 h-8 text-primary"></i>
            </div>
            <i data-lucide="arrow-right" class="w-5 h-5 text-muted group-hover:text-primary"></i>
          </div>

          <h4 class="text-lg font-black uppercase text-primary mb-2">
            Tablas Básicas
          </h4>
          <p class="text-xs text-secondary font-medium">
            Gestión centralizada de Juegos, Roles y Géneros.
          </p>
        </a>

        <a href="usuarios/index.php" class="group flex flex-col p-8 bg-surface border-2 border-primary transition-all shadow-hard-sm hover:shadow-hard">
          <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-subtle">
              <i data-lucide="users" class="w-8 h-8 text-primary"></i>
            </div>
            <i data-lucide="arrow-right" class="w-5 h-5 text-muted group-hover:text-primary transition-all"></i>
          </div>
          <h4 class="text-lg font-black uppercase text-primary mb-2">
            Gestión de Usuarios
          </h4>
          <p class="text-xs text-secondary font-medium">
            Administrar permisos y roles de usuarios.
          </p>
        </a>

        <a href="backup/index.php" class="group flex flex-col p-8 bg-surface border-2 border-primary transition-all shadow-hard-sm hover:shadow-hard">
          <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-subtle">
              <i data-lucide="database" class="w-8 h-8 text-primary"></i>
            </div>
            <i data-lucide="arrow-right" class="w-5 h-5 text-muted group-hover:text-primary"></i>
          </div>
          <h3 class="text-xl font-black uppercase text-primary mb-2">Respaldo BD</h3>
          <p class="text-xs text-secondary font-medium">Respaldar y Restaurar Base de Datos</p>
        </a>

      </div>
    </div>
  </main>
</div>

<?php include '../../includes/footer.php'; ?>
