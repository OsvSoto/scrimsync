<?php
// modules/admin/backup/index.php
session_start();
require_once '../../../config/db.php';

// Validar Admin
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo'] != 0) {
    header("Location: ../../../modules/auth/login.php");
    exit;
}

include '../../../includes/header.php';
?>

<?php include '../../../includes/admin_navbar.php'; ?>

<div class="min-h-screen bg-background">
    <main class="flex-1 w-full pt-16 pb-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="../dashboard.php" class="text-secondary hover:text-primary text-xs font-bold uppercase tracking-widest mb-6 inline-block">
                <i data-lucide="arrow-left" class="w-3 h-3 inline mr-1"></i> Volver al Dashboard
            </a>

            <header class="mb-10">
                <h2 class="text-3xl font-black text-primary uppercase tracking-tight mb-2">
                    Copia de Seguridad
                </h2>
                <p class="text-secondary text-sm">Respalda o restaura la base de datos del sistema.</p>
            </header>

            <?php if (isset($_GET['msg'])): ?>
                <div class="mb-6 bg-success-light border-2 border-success-border p-4 shadow-hard-success flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <i data-lucide="check-circle" class="text-success-text w-5 h-5 shrink-0 mt-0.5"></i>
                        <p class="text-success-text font-black uppercase text-xs tracking-widest leading-relaxed">
                            <?php
                            if ($_GET['msg'] == 'restored') echo "Base de datos restaurada correctamente.";
                            ?>
                        </p>
                    </div>
                    <button onclick="this.parentElement.remove();" class="text-success-text hover:opacity-70 shrink-0">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="mb-6 bg-error-light border-2 border-error-border p-4 shadow-hard-error flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-circle" class="text-error-text w-5 h-5 shrink-0 mt-0.5"></i>
                        <p class="text-error-text font-black uppercase text-xs tracking-widest leading-relaxed">
                            Error: <?php echo htmlspecialchars($_GET['error']); ?>
                        </p>
                    </div>
                    <button onclick="this.parentElement.remove();" class="text-error-text hover:opacity-70 shrink-0">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Exportar -->
                <div class="bg-surface border-2 border-primary p-8 shadow-hard">
                    <div class="p-4 bg-subtle w-fit mb-6">
                        <i data-lucide="download" class="w-8 h-8 text-primary"></i>
                    </div>
                    <h3 class="text-xl font-black uppercase text-primary mb-2">Exportar Base de Datos</h3>
                    <p class="text-sm text-secondary mb-8">Descarga una copia completa de la base de datos en formato .zip.</p>

                    <form action="controller_backup.php" method="POST">
                        <input type="hidden" name="action" value="export">
                        <button type="submit" class="w-full bg-primary text-white py-4 font-black uppercase tracking-widest text-xs hover:bg-primary-hover transition-all flex items-center justify-center gap-2 shadow-hard hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] cursor-pointer">
                            <i data-lucide="file-archive" class="w-4 h-4"></i>
                            Generar Respaldo
                        </button>
                    </form>
                </div>

                <!-- Importar -->
                <div class="bg-surface border-2 border-primary p-8 shadow-hard">
                    <div class="p-4 bg-subtle w-fit mb-6">
                        <i data-lucide="upload" class="w-8 h-8 text-primary"></i>
                    </div>
                    <h3 class="text-xl font-black uppercase text-primary mb-2">Importar Base de Datos</h3>
                    <p class="text-sm text-error-text mb-8 font-bold uppercase">¡ATENCIÓN! Esta acción reemplaza todos los datos actuales por los del archivo importado.</p>

                    <form action="controller_backup.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="import">
                        <div class="mb-6">
                            <label class="block text-[10px] font-black uppercase text-secondary mb-2 tracking-widest">Subir .zip</label>
                            <input type="file" name="backup_file" accept=".zip" required
                                class="w-full bg-background border-2 hover:border-primary text-primary px-4 py-3 font-bold text-sm focus:border-primary file:rounded-none file:px-4 file:font-black cursor-pointer">
                        </div>
                        <button type="submit" onclick="return confirm('¿Estás seguro? Todos los datos actuales se perderán.')" class="w-full bg-primary text-white hover:bg-primary-hover py-4 font-black uppercase tracking-widest text-xs transition-all flex items-center justify-center gap-2 shadow-hard hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] cursor-pointer">
                            <i data-lucide="refresh-ccw" class="w-4 h-4"></i>
                            Restaurar desde .zip
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../../../includes/footer.php'; ?>
