<?php
// modules/admin/mantencion/index.php
session_start();
require_once '../../../config/db.php';
include '../../../includes/header.php';

// Validar Admin
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo'] != 0) {
  header("Location: ../../../modules/auth/login.php");
  exit;
}
?>

<?php include '../../../includes/admin_navbar.php'; ?>

<div class="min-h-screen bg-background">

  <?php # include '../../../includes/admin_sidebar.php'; ?>

  <main class="flex-1 w-full pt-16 pb-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <a href="../dashboard.php" class="text-secondary hover:text-primary text-xs font-bold uppercase tracking-widest mb-6 inline-block">
        <i data-lucide="arrow-left" class="w-3 h-3 inline mr-1"></i> Volver al Dashboard
      </a>

      <header class="mb-10">
        <h2 class="text-3xl font-black text-primary uppercase tracking-tight mb-2">
          Mantención de Tablas Básicas
        </h2>
        <p class="text-secondary text-sm">Seleccione la tabla maestra que desea administrar.</p>
      </header>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <a href="../games/index.php" class="group block bg-surface border-2 border-primary p-8 transition-all shadow-hard-sm hover:shadow-hard">
          <div class="mb-4 text-primary group-hover:scale-110 transition-transform origin-left">
            <i data-lucide="gamepad-2" class="w-10 h-10"></i>
          </div>
          <h3 class="text-xl font-black uppercase text-primary mb-2">1. Juegos</h3>
          <p class="text-xs text-secondary font-medium">Ver listado, crear y editar.</p>
        </a>

        <a href="../roles/index.php" class="group block bg-surface border-2 border-primary p-8 transition-all shadow-hard-sm hover:shadow-hard">
          <div class="mb-4 text-primary group-hover:scale-110 transition-transform origin-left">
            <i data-lucide="shield" class="w-10 h-10"></i>
          </div>
          <h3 class="text-xl font-black uppercase text-primary mb-2">2. Roles</h3>
          <p class="text-xs text-secondary font-medium">Roles predefinidos por juego</p>
        </a>

        <a href="../genero/index.php" class="group block bg-surface border-2 border-primary p-8 transition-all shadow-hard-sm hover:shadow-hard">
          <div class="mb-4 text-primary group-hover:scale-110 transition-transform origin-left">
            <i data-lucide="tags" class="w-10 h-10"></i>
          </div>
          <h3 class="text-xl font-black uppercase text-primary mb-2">3. Géneros</h3>
          <p class="text-xs text-secondary font-medium">Administrar categorías</p>
        </a>

      </div>
    </div>
  </main>
</div>

<?php include '../../../includes/footer.php'; ?>
