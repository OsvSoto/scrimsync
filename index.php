<?php
session_start();

// LOGICA DE SEGURIDAD (Sin cambios para evitar bucles)
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 0) {
        header("Location: modules/admin/dashboard.php");
        exit;
    }
}
include 'includes/header.php';
?>

<body class="bg-background text-zinc-900 font-sans antialiased selection:bg-black selection:text-white">

    <nav class="w-full border-b border-gray-200 bg-white/80 backdrop-blur-md fixed top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">

                <div class="flex-shrink-0 flex items-center gap-3">
                    <img src="assets/img/logo.png" alt="Logo ScrimSync" class="h-20 w-auto object-contain">

                    <span class="font-black text-2xl tracking-tighter">SCRIMSYNC</span>
                </div>

                <div class="flex items-center gap-6">
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>

                        <div class="hidden md:flex flex-col items-end mr-2">
                            <span class="text-xs font-bold text-secondary uppercase tracking-widest">Jugador</span>
                            <span class="text-sm font-bold text-black"><?php echo htmlspecialchars($_SESSION['alias']); ?></span>
                        </div>

                        <a href="modules/auth/logout.php"
                           class="text-sm font-bold text-secondary hover:text-red-600 transition-colors">
                            SALIR
                        </a>

                    <?php else: ?>

                        <a href="modules/auth/login.php" class="text-sm font-bold text-zinc-600 hover:text-black transition-colors">
                            INICIAR SESIÓN
                        </a>
                        <a href="modules/auth/register.php"
                           class="bg-black text-white px-6 py-3 text-sm font-bold uppercase tracking-wider rounded-lg hover:bg-zinc-800 transition-all shadow-lg shadow-zinc-300">
                            CREAR CUENTA
                        </a>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-32 pb-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto flex flex-col items-center text-center">

        <span class="bg-gray-100 text-gray-800 text-xs font-bold px-3 py-1 rounded-full border border-gray-200 mb-6 uppercase tracking-widest">
            Gestión de eSports v1.0
        </span>

        <h1 class="text-5xl md:text-7xl font-black text-black tracking-tighter mb-6 leading-[1.1]">
            ELEVA TU NIVEL <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-zinc-500 to-black">COMPETITIVO</span>
        </h1>

        <p class="text-xl text-secondary max-w-2xl mb-10 leading-relaxed">
            La plataforma centralizada para organizar scrims, analizar resultados y gestionar tu equipo. Simple, rápido y profesional.
        </p>

        <?php if (isset($_SESSION['loggedin'])): ?>
            <div class="w-full max-w-4xl grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="bg-surface p-6 rounded-xl border border-gray-200 hover:border-black transition-colors cursor-pointer group">
                    <h3 class="font-bold text-lg mb-2 group-hover:underline">Buscar Partida</h3>
                    <p class="text-sm text-secondary">Encuentra rivales de tu mismo nivel.</p>
                </div>
                <div class="bg-surface p-6 rounded-xl border border-gray-200 hover:border-black transition-colors cursor-pointer group">
                    <h3 class="font-bold text-lg mb-2 group-hover:underline">Mi Equipo</h3>
                    <p class="text-sm text-secondary">Gestiona roster y estrategias.</p>
                </div>
                <div class="bg-surface p-6 rounded-xl border border-gray-200 hover:border-black transition-colors cursor-pointer group">
                    <h3 class="font-bold text-lg mb-2 group-hover:underline">Historial</h3>
                    <p class="text-sm text-secondary">Revisa tus últimos resultados.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="flex flex-col sm:flex-row gap-4 w-full justify-center">
                <a href="modules/auth/register.php" class="bg-black text-white px-8 py-4 text-base font-bold rounded-lg hover:scale-105 transition-transform shadow-xl shadow-zinc-200">
                    EMPEZAR AHORA
                </a>
                <a href="#features" class="px-8 py-4 text-base font-bold text-black border-2 border-gray-200 rounded-lg hover:border-black hover:bg-white transition-colors">
                    SABER MÁS
                </a>
            </div>
        <?php endif; ?>

    </main>

    <footer class="border-t border-gray-200 mt-20 py-10 bg-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-secondary text-sm font-medium">&copy; <?php echo date("Y"); ?> ScrimSync. Todos los derechos reservados.</p>
        </div>
    </footer>

</body>
</html>
