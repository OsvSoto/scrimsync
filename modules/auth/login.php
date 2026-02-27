<?php
session_start();
require_once '../../config/db.php';
include '../../includes/header.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if ($_SESSION['tipo'] == 0) {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: ../../index.php');
    }
    exit;
}
?>

<div class="min-h-screen flex items-center justify-center bg-background px-4">

    <div class="relative w-full max-w-lg">
        <?php if (isset($_GET['error']) || (isset($_GET['msg']) && $_GET['msg'] == 'registered')): ?>
            <div class="absolute bottom-full left-0 w-full mb-4 space-y-2">
                <?php if (isset($_GET['error'])): ?>
                    <div class="bg-error-light border-2 border-error-border p-3 shadow-hard-error flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <i data-lucide="alert-circle" class="text-error-text w-5 h-5 shrink-0 mt-0.5"></i>
                            <p class="text-error-text font-black uppercase text-[10px] tracking-widest leading-relaxed text-left">
                                <?php
                                if ($_GET['error'] == 'invalid_credentials')
                                    echo 'Usuario o contraseña incorrectos.';
                                elseif ($_GET['error'] == 'unknown_op')
                                    echo 'Operación no válida.';
                                else
                                    echo 'Ocurrió un error inesperado.';
                                ?>
                            </p>
                        </div>
                        <button onclick="this.parentElement.remove();" class="text-error-text hover:opacity-70 shrink-0">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'registered'): ?>
                    <div class="bg-success-light border-2 border-success-border p-3 shadow-hard-success flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <i data-lucide="check-circle" class="text-success-text w-5 h-5 shrink-0 mt-0.5"></i>
                            <p class="text-success-text font-black uppercase text-[10px] tracking-widest leading-relaxed text-left">
                                ¡Registro exitoso! Ahora puedes ingresar.
                            </p>
                        </div>
                        <button onclick="this.parentElement.remove();" class="text-success-text hover:opacity-70 shrink-0">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="w-full bg-surface border-2 border-primary p-8 shadow-hard">

            <div class="text-center mb-8">
                <h1 class="text-4xl font-black text-primary tracking-tighter mb-2">SCRIMSYNC</h1>
                <p class="text-secondary text-sm uppercase tracking-widest font-bold">Iniciar Sesión</p>
            </div>

            <form action="controller_auth.php" method="POST" class="space-y-6">

            <input type="hidden" name="p_op" value="Login">

            <div>
                <label class="block text-[10px] font-black uppercase text-secondary mb-2 tracking-widest">Usuario</label>
                <input type="text" name="username" required
                       class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none transition-colors">
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase text-secondary mb-2 tracking-widest">Contraseña</label>
                <input type="password" name="password" required
                       class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none transition-colors">
            </div>

            <button type="submit" class="w-full bg-primary text-white py-4 font-black uppercase tracking-widest text-xs hover:bg-primary-hover transition-all hover:translate-x-[2px] hover:translate-y-[2px] shadow-hard-sm hover:shadow-none">
                Ingresar
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="recover.php" class="text-xs text-secondary font-bold hover:text-primary transition-colors">¿Olvidaste tu contraseña?</a>
        </div>

        <div class="mt-8 text-center border-t-2 border-subtle pt-6">
            <p class="text-xs text-secondary font-bold">
                ¿No tienes cuenta?
                <a href="register.php" class="text-primary hover:underline">Regístrate aquí</a>
            </p>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
