<?php
//index.php -- Pagina Principal
session_start();

// LOGICA DE SEGURIDAD
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  if (isset($_SESSION['rol']) && $_SESSION['rol'] === 0) {
    header("Location: modules/admin/dashboard.php");
    exit;
  }
}
$bodyClass = "bg-background text-primary font-sans antialiased selection:bg-black selection:text-white min-h-screen flex flex-col";
include 'includes/header.php';
?>

<nav class="fixed top-0 left-0 w-full h-16 bg-surface border-b-2 border-primary z-50 flex items-center justify-between gap-4 px-4 md:px-6">
  <div class="flex items-center">
    <a href="index.php" class="flex items-center gap-2">
      <span class="text-2xl font-black tracking-tighter text-primary">SCRIMSYNC</span>
    </a>
  </div>

  <div class="flex items-center gap-4 md:gap-6">
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
      <a href="modules/user/profile/index.php" class="flex items-center gap-3 group">
        <div class="md:flex flex-col items-end">
          <span class="text-xs font-black text-primary"><?php echo htmlspecialchars($_SESSION['alias'] ?? ''); ?></span>
        </div>
        <div class="p-1.5 bg-subtle rounded-sm group-hover:bg-border transition-colors border border-border group-hover:border-primary">
          <i data-lucide="user" class="w-4 h-4 text-primary"></i>
        </div>
      </a>
      <a href="modules/user/notification/index.php" class="flex items-center gap-3 group">
        <div class="p-1.5 bg-subtle rounded-sm group-hover:bg-border transition-colors border border-border group-hover:border-primary">
          <i data-lucide="bell" class="w-4 h-4 text-primary"></i>
        </div>
      </a>
      <a href="modules/auth/logout.php"
        class="text-xs font-black text-secondary hover:text-error-text transition-colors uppercase tracking-widest flex items-center gap-2">
        <i data-lucide="log-out" class="w-4 h-4"></i>
        <span class="hidden md:inline">SALIR</span>
      </a>
    <?php else: ?>
      <a href="modules/auth/login.php" class="text-xs font-black text-secondary hover:text-primary text-center transition-colors uppercase tracking-widest">
        INICIAR SESIÓN
      </a>
      <a href="modules/auth/register.php"
        class="bg-primary text-white px-4 py-2.5 text-xs font-black uppercase tracking-widest text-center hover:bg-primary-hover transition-all shadow-hard-sm">
        CREAR CUENTA
      </a>
    <?php endif; ?>
  </div>
</nav>

<main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
    <div class="flex flex-col items-start text-left">
      <span class="bg-subtle text-primary text-xs font-bold px-3 py-1 rounded-full border border-border mb-6 uppercase tracking-widest">
        Gestión de eSports
      </span>

      <h1 class="text-5xl md:text-7xl font-black text-primary tracking-tighter mb-6 leading-[1.1]">
        ELEVA TU NIVEL <br>
        COMPETITIVO
      </h1>

      <p class="text-xl text-secondary max-w-lg mb-10 leading-relaxed">
        La plataforma centralizada para organizar scrims y gestionar tu equipo. Simple, rápido y profesional.
      </p>

      <?php if (!isset($_SESSION['loggedin'])): ?>
        <div class="flex flex-col sm:flex-row gap-4 w-full">
          <a href="modules/auth/register.php" class="bg-primary text-white px-8 py-4 text-base font-bold rounded-lg hover:scale-105 transition-transform shadow-hard text-center">
            EMPEZAR AHORA
          </a>
          <a href="#features" class="px-8 py-4 text-base font-bold text-primary border-2 border-border rounded-lg hover:border-primary hover:bg-surface transition-colors text-center">
            SABER MÁS
          </a>
        </div>
      <?php endif; ?>
    </div>

    <div class="flex justify-center">
      <img src="assets/img/logo.png" alt="ScrimSync Hero" class="w-full max-w-md object-contain drop-shadow-xl">
    </div>

  </div>

  <?php if (isset($_SESSION['loggedin'])): ?>
    <div class="w-full grid grid-cols-1 md:grid-cols-4 gap-6">
      <a href="#">
        <div class="bg-surface p-6 rounded-xl border border-border hover:border-primary transition-colors cursor-pointer group text-left shadow-sm hover:shadow-md">
          <div class="flex items-center gap-2 mb-2 group-hover:underline">
            <i data-lucide="search" class="w-5 h-5"></i>
            <h3 class="font-bold text-lg">Buscar Partida</h3>
          </div>
          <p class="text-sm text-secondary">Encuentra tu proximo rival.</p>
        </div>
      </a>
      <a href="modules/team/profile/index.php">
        <div class="bg-surface p-6 rounded-xl border border-border hover:border-primary transition-colors cursor-pointer group text-left shadow-sm hover:shadow-md">
          <div class="flex items-center gap-2 mb-2 group-hover:underline">
            <i data-lucide="shield" class="w-5 h-5"></i>
            <h3 class="font-bold text-lg">Mi Equipo</h3>
          </div>
          <p class="text-sm text-secondary">Gestiona roster y estrategias.</p>
        </div>
      </a>


      <!-- <a href="modules/user/calendar/index.php"> -->
      <a href="#">
        <div class="bg-surface p-6 rounded-xl border border-border hover:border-primary transition-colors cursor-pointer group text-left shadow-sm hover:shadow-md">
          <div class="flex items-center gap-2 mb-2 group-hover:underline">
            <i data-lucide="calendar" class="w-5 h-5"></i>
            <h3 class="font-bold text-lg">Calendario</h3>
          </div>
          <p class="text-sm text-secondary">Revisa tu calendario.</p>
        </div>
      </a>

      <a href="modules/user/profile/index.php">
        <div class="bg-surface p-6 rounded-xl border border-border hover:border-primary transition-colors cursor-pointer group text-left shadow-sm hover:shadow-md">
          <div class="flex items-center gap-2 mb-2 group-hover:underline">
            <i data-lucide="user" class="w-5 h-5"></i>
            <h3 class="font-bold text-lg">Mi Perfil</h3>
          </div>
          <p class="text-sm text-secondary">Modifica los datos de tu perfil.</p>
        </div>
      </a>
    </div>
  <?php endif; ?>

</main>

<footer id="footer" class="w-full border-t bg-white py-8 mt-auto">
  <div class="max-w-7xl mx-auto px-4 text-center text-muted text-sm font-medium">
    &copy; <?php echo date("Y"); ?> ScrimSync. Todos los derechos reservados.
  </div>
</footer>

<?php include 'includes/footer.php'; ?>
