<?php
session_start();
require_once '../../config/db.php';
include '../../includes/header.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if ($_SESSION['rol'] == 0) {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../../index.php");
    }
    exit;
}
?>

<div class="min-h-screen flex items-center justify-center bg-background px-4">

    <div class="relative w-full max-w-lg">
        <?php if (isset($_GET['error']) || (isset($_GET['msg']) && $_GET['msg'] == 'registered')): ?>
            <div class="absolute bottom-full left-0 w-full mb-4">
                <?php if (isset($_GET['error'])): ?>
                    <div class="bg-rose-100 border-2 border-rose-500 text-rose-700 p-3 text-xs font-bold uppercase tracking-wide text-center shadow-[4px_4px_0px_0px_rgba(244,63,94,0.2)]">
                        <?php
                            if ($_GET['error'] == 'invalid_credentials') echo "Usuario o contraseña incorrectos.";
                            elseif ($_GET['error'] == 'unknown_op') echo "Operación no válida.";
                            else echo "Ocurrió un error inesperado.";
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'registered'): ?>
                    <div class="bg-emerald-100 border-2 border-emerald-500 text-emerald-700 p-3 text-xs font-bold uppercase tracking-wide text-center shadow-[4px_4px_0px_0px_rgba(16,185,129,0.2)]">
                        ¡Registro exitoso! Ahora puedes ingresar.
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="w-full bg-surface border-2 border-primary p-8 shadow-[8px_8px_0px_0px_rgba(9,9,11,1)]">

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

            <button type="submit" class="w-full bg-primary text-white py-4 font-black uppercase tracking-widest text-xs hover:bg-zinc-800 transition-all hover:translate-x-[2px] hover:translate-y-[2px] shadow-[4px_4px_0px_0px_rgba(0,0,0,0.2)] hover:shadow-none">
                Ingresar
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="recover.php" class="text-xs text-secondary font-bold hover:text-primary transition-colors">¿Olvidaste tu contraseña?</a>
        </div>

        <div class="mt-8 text-center border-t-2 border-zinc-100 pt-6">
            <p class="text-xs text-secondary font-bold">
                ¿No tienes cuenta?
                <a href="register.php" class="text-primary hover:underline">Regístrate aquí</a>
            </p>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
