<?php
session_start();
require_once '../../config/db.php';
include '../../includes/header.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if ($_SESSION['tipo'] == 0) {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../../index.php");
    }
    exit;
}
?>

<div class="min-h-screen flex items-center justify-center bg-background px-4">
    <div class="relative w-full max-w-md">
        <?php if (isset($_GET['msg']) || isset($_GET['error'])): ?>
            <div class="absolute bottom-full left-0 w-full mb-4 space-y-2">
                <?php if (isset($_GET['msg'])): ?>
                    <?php if ($_GET['msg'] == 'email_sent'): ?>
                        <div class="bg-success-light border-2 border-success-border p-3 shadow-hard-success flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <i data-lucide="check-circle" class="text-success-text w-5 h-5 shrink-0 mt-0.5"></i>
                                <p class="text-success-text font-black uppercase text-[10px] tracking-widest leading-relaxed text-left">
                                    Si el correo existe, se ha enviado una nueva contraseña.
                                </p>
                            </div>
                            <button onclick="this.parentElement.remove();" class="text-success-text hover:opacity-70 shrink-0">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                    <div class="bg-error-light border-2 border-error-border p-3 shadow-hard-error flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <i data-lucide="alert-circle" class="text-error-text w-5 h-5 shrink-0 mt-0.5"></i>
                            <p class="text-error-text font-black uppercase text-[10px] tracking-widest leading-relaxed text-left">
                                <?php if ($_GET['error'] == 'failed') echo "No se pudo procesar la solicitud."; ?>
                            </p>
                        </div>
                        <button onclick="this.parentElement.remove();" class="text-error-text hover:opacity-70 shrink-0">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="w-full bg-surface border-2 border-primary p-8 shadow-hard">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-black text-primary tracking-tighter mb-2">SCRIMSYNC</h1>
                <p class="text-secondary text-sm uppercase tracking-widest font-bold">Recuperar Contraseña</p>
            </div>

            <form action="controller_auth.php" method="POST" class="space-y-6">
            <input type="hidden" name="p_op" value="Recuperar">
            <div>
                <label class="block text-[10px] font-black uppercase text-secondary mb-2 tracking-widest">Correo Electrónico</label>
                <input type="email" name="email" required
                       class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none transition-colors">
            </div>
            <button type="submit" class="w-full bg-primary text-white py-4 font-black uppercase tracking-widest text-xs hover:bg-primary-hover transition-all hover:translate-x-[2px] hover:translate-y-[2px] shadow-hard-sm hover:shadow-none">
                Recuperar
            </button>
        </form>

        <div class="mt-8 text-center border-t-2 border-subtle pt-6">
            <p class="text-xs text-secondary font-bold">
                <a href="login.php" class="text-primary hover:underline">Volver al inicio de sesión</a>
            </p>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
