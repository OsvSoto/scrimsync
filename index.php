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
$bodyClass = "bg-background text-zinc-900 font-sans antialiased selection:bg-black selection:text-white min-h-screen flex flex-col";
include 'includes/header.php';
?>
<nav class="fixed top-0 left-0 w-full h-16 bg-surface border-b-2 border-primary z-50 flex items-center justify-between px-6 bg-white/90 backdrop-blur-sm">
  <div class="flex items-center gap-3">
    <a href="index.php" class="flex items-center gap-2">
      <span class="font-black text-2xl tracking-tighter text-primary">SCRIMSYNC</span>
    </a>
  </div>

  <div class="flex items-center gap-6">
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
      <a href="modules/user/profile/index.php" class="flex items-center gap-2 group">
        <div class="hidden md:flex flex-col items-end">
          <span class="text-[10px] font-black text-secondary uppercase tracking-widest group-hover:text-primary transition-colors">Jugador</span>
          <span class="text-xs font-black text-primary"><?php echo htmlspecialchars($_SESSION['alias'] ?? ''); ?></span>
        </div>
        <div class="p-1.5 bg-zinc-100 rounded-sm group-hover:bg-zinc-200 transition-colors border border-zinc-200 group-hover:border-primary">
          <i data-lucide="user" class="w-4 h-4 text-primary"></i>
        </div>
      </a>
      <a href="modules/auth/logout.php"
        class="text-xs font-black text-secondary hover:text-rose-600 transition-colors uppercase tracking-widest flex items-center gap-2">
        <i data-lucide="log-out" class="w-4 h-4"></i>
        <span class="hidden md:inline">SALIR</span>
      </a>
    <?php else: ?>
      <a href="modules/auth/login.php" class="text-xs font-black text-zinc-500 hover:text-primary transition-colors uppercase tracking-widest">
        INICIAR SESIÓN
      </a>
      <a href="modules/auth/register.php"
        class="bg-primary text-white px-5 py-2.5 text-xs font-black uppercase tracking-widest hover:bg-zinc-800 transition-all shadow-[4px_4px_0px_0px_rgba(0,0,0,0.1)]">
        CREAR CUENTA
      </a>
    <?php endif; ?>
  </div>
</nav>

<main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-16">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
    <div class="flex flex-col items-start text-left">
      <span class="bg-gray-100 text-gray-800 text-xs font-bold px-3 py-1 rounded-full border border-gray-200 mb-6 uppercase tracking-widest">
        Gestión de eSports v1.0
      </span>

      <h1
        id="spotlight-text"
        class="text-5xl md:text-7xl font-black tracking-tighter mb-6 leading-[1.1] text-transparent bg-clip-text cursor-default w-full"
        style="
            background-image: radial-gradient(circle 650px at var(--x, 50%) var(--y, 50%), #71717a 0%, #000000 50%);
            -webkit-background-clip: text;
            background-clip: text;
        ">
        ELEVA TU NIVEL <br>
        <span>COMPETITIVO</span>
      </h1>

      <p class="text-xl text-secondary max-w-lg mb-10 leading-relaxed">
        La plataforma centralizada para organizar scrims y gestionar tu equipo. Simple, rápido y profesional.
      </p>

      <?php if (!isset($_SESSION['loggedin'])): ?>
        <div class="flex flex-col sm:flex-row gap-4 w-full">
          <a href="modules/auth/register.php" class="bg-black text-white px-8 py-4 text-base font-bold rounded-lg hover:scale-105 transition-transform shadow-xl shadow-zinc-200 text-center">
            EMPEZAR AHORA
          </a>
          <a href="#features" class="px-8 py-4 text-base font-bold text-black border-2 border-gray-200 rounded-lg hover:border-black hover:bg-white transition-colors text-center">
            SABER MÁS
          </a>
        </div>
      <?php endif; ?>
    </div>

    <div class="flex justify-center lg:justify-end">
      <img src="assets/img/logo.png" alt="ScrimSync Hero" class="w-full max-w-md object-contain drop-shadow-xl">
    </div>

  </div>

  <?php if (isset($_SESSION['loggedin'])): ?>
    <div class="w-full grid grid-cols-1 md:grid-cols-3 gap-6">
      <a>
        <div class="bg-surface p-6 rounded-xl border border-gray-200 hover:border-black transition-colors cursor-pointer group text-left shadow-sm hover:shadow-md">
          <h3 class="font-bold text-lg mb-2 group-hover:underline">Buscar Partida</h3>
          <p class="text-sm text-secondary">Encuentra rivales de tu mismo nivel.</p>
        </div>
      </a>
      <a href="modules/team/profile/index.php">
        <div class="bg-surface p-6 rounded-xl border border-gray-200 hover:border-black transition-colors cursor-pointer group text-left shadow-sm hover:shadow-md">
          <h3 class="font-bold text-lg mb-2 group-hover:underline">Mi Equipo</h3>
          <p class="text-sm text-secondary">Gestiona roster y estrategias.</p>
        </div>
      </a>
      <a href="modules/user/profile/index.php">
        <div class="bg-surface p-6 rounded-xl border border-gray-200 hover:border-black transition-colors cursor-pointer group text-left shadow-sm hover:shadow-md">
          <h3 class="font-bold text-lg mb-2 group-hover:underline">
            Mi Perfil
          </h3>
          <p class="text-sm text-secondary">Modifica los datos de tu perfil.</p>
        </div>
      </a>
    </div>
  <?php endif; ?>

</main>

<footer id="footer" class="w-full border-t bg-white py-8 mt-auto">
  <div class="max-w-7xl mx-auto px-4 text-center text-zinc-400 text-sm font-medium">
    &copy; <?php echo date("Y"); ?> ScrimSync. Todos los derechos reservados.
  </div>
</footer>

<script>
  const text = document.getElementById('spotlight-text');
  if (text) {
    text.addEventListener('mousemove', (e) => {
      const rect = text.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      text.style.setProperty('--x', `${x}px`);
      text.style.setProperty('--y', `${y}px`);
    });
  }
</script>
<?php include 'includes/footer.php'; ?>
