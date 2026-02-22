<?php
// index.php -- Pagina Principal
session_start();
require_once 'config/db.php';

// LOGICA DE SEGURIDAD
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 0) {
        header('Location: modules/admin/dashboard.php');
        exit;
    }
}

$bodyClass = 'bg-[#fefcff] text-primary font-sans antialiased selection:bg-black selection:text-white min-h-screen flex flex-col relative overflow-x-hidden';
include 'includes/header.php';
?>

<!-- Glow Gradient -->
<div class="fixed inset-0 z-0 pointer-events-none" style="
      background-image:
        radial-gradient(circle at 30% 70%, rgba(181, 190, 230, 0.35), transparent 60%),
        radial-gradient(circle at 70% 30%, rgba(99, 117, 203, 0.25), transparent 60%);
">
</div>

<?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
    <?php include 'includes/user_navbar.php'; ?>
<?php else: ?>
    <nav class="fixed top-0 left-0 w-full h-10 bg-surface border-b-2 border-primary z-50">
        <div class="max-w-6xl mx-auto h-full flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center">
                <a href="index.php" class="flex items-center gap-2">
                    <span class="text-2xl font-black tracking-tighter text-primary transition-colors hover:text-scrimsync">SCRIMSYNC</span>
                </a>
            </div>

            <div class="flex items-center gap-2">
                <a href="modules/auth/login.php"
                    class="text-xs text-secondary uppercase font-black hover:text-scrimsync text-center transition-colors  tracking-widest hover:underline">
                    Iniciar Sesión
                </a>
                <p class="cursor-default font-bold">/</p>
                <a href="modules/auth/register.php"
                    class="text-xs text-secondary uppercase font-black hover:text-scrimsync text-center transition-colors  tracking-widest hover:underline">
                    Crear Cuenta
                </a>
            </div>
        </div>
    </nav>
<?php endif; ?>

<main class="relative z-10 grow w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-12">
        <div class="flex flex-col items-start text-left">
            <span class="bg-subtle text-primary text-xs font-bold px-3 py-1 rounded-none border border-border mb-6 uppercase tracking-widest">
                Gestión de eSports
            </span>

            <h1 class="text-5xl md:text-7xl font-black text-primary tracking-tighter mb-6 leading-[1.1]">
                ELEVA TU NIVEL <br>
                COMPETITIVO
            </h1>

            <p class="text-xl text-secondary max-w-lg mb-10 leading-relaxed">
                La plataforma centralizada para organizar scrims y gestionar tu equipo.
            </p>

            <?php if (!isset($_SESSION['loggedin'])): ?>
                <div class="flex flex-col sm:flex-row gap-4 w-full">
                    <a href="modules/auth/register.php"
                        class="bg-surface px-8 py-4 text-base font-bold text-primary border border-primary rounded-none transition-colors text-center shadow-hard-sm hover:shadow-hard">
                        EMPEZAR AHORA
                    </a>
                    <a href="#features"
                        class="bg-surface px-8 py-4 text-base font-bold text-primary border border-primary rounded-none transition-colors text-center shadow-hard-sm hover:shadow-hard">
                        SABER MÁS
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="block flex justify-center">
            <img src="assets/img/logo.png" alt="ScrimSync Hero" class="w-full max-w-md object-contain drop-shadow-xl">
        </div>

    </div>

    <?php if (isset($_SESSION['loggedin'])): ?>
        <div class="w-full grid grid-cols-1 md:grid-cols-4 gap-6">
            <a href="modules/scrim/index.php">
                <div class="bg-surface p-6 rounded-none border border-primary transition-all cursor-pointer group text-left shadow-hard-sm hover:shadow-hard">
                    <div class="flex items-center gap-2 mb-2 group-hover:underline">
                        <i data-lucide="search" class="w-5 h-5"></i>
                        <h3 class="font-bold text-lg">Buscar Scrim</h3>
                    </div>
                    <p class="text-sm text-secondary">Encuentra tu proximo rival.</p>
                </div>
            </a>
            <a href="modules/team/profile/index.php">
                <div class="bg-surface p-6 rounded-none border border-primary transition-all cursor-pointer group text-left shadow-hard-sm hover:shadow-hard">
                    <div class="flex items-center gap-2 mb-2 group-hover:underline">
                        <i data-lucide="shield" class="w-5 h-5"></i>
                        <h3 class="font-bold text-lg">Mi Equipo</h3>
                    </div>
                    <p class="text-sm text-secondary">Gestiona roster y estrategias.</p>
                </div>
            </a>


            <a href="modules/user/calendar/index.php">
                <div class="bg-surface p-6 rounded-none border border-primary transition-all cursor-pointer group text-left shadow-hard-sm hover:shadow-hard">
                    <div class="flex items-center gap-2 mb-2 group-hover:underline">
                        <i data-lucide="calendar" class="w-5 h-5"></i>
                        <h3 class="font-bold text-lg">Calendario</h3>
                    </div>
                    <p class="text-sm text-secondary">Revisa tu calendario.</p>
                </div>
            </a>

            <a href="modules/user/profile/index.php">
                <div class="bg-surface p-6 rounded-none border border-primary transition-all cursor-pointer group text-left shadow-hard-sm hover:shadow-hard">
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

<footer id="footer" class="relative z-10 w-full border-t py-3 mt-auto">
    <div class="max-w-7xl mx-auto px-4 text-center text-muted text-sm font-medium">
        &copy; <?php echo date('Y'); ?> ScrimSync.
    </div>
</footer>

<?php include 'includes/footer.php'; ?>
