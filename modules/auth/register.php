<?php
// modules/auth/register.php
session_start();
require_once '../../config/db.php';
include '../../includes/header.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: ../../index.php");
    exit;
}
?>

<div class="min-h-screen flex items-center justify-center bg-background px-4 py-12">

    <div class="relative w-full max-w-lg">
        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="absolute bottom-full left-0 w-full mb-4">
                <div class="bg-error-light border-2 border-error-border p-3 shadow-hard-error flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <i data-lucide="alert-circle" class="text-error-text w-5 h-5 shrink-0"></i>
                        <p class="text-error-text font-black uppercase text-[10px] tracking-widest leading-relaxed text-left">
                            <?php echo htmlspecialchars($_SESSION['flash_error']); ?>
                        </p>
                    </div>
                    <button onclick="this.parentElement.remove();" class="text-error-text hover:opacity-70 shrink-0">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_msg'])): ?>
            <div class="absolute bottom-full left-0 w-full mb-4">
                <div class="bg-success-light border-2 border-success-border p-3 shadow-hard-success flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle" class="text-success-text w-5 h-5 shrink-0"></i>
                        <p class="text-success-text font-black uppercase text-[10px] tracking-widest leading-relaxed text-left">
                            <?php echo htmlspecialchars($_SESSION['flash_msg']); ?>
                        </p>
                    </div>
                    <button onclick="this.parentElement.remove();" class="text-success-text hover:opacity-70 shrink-0">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            <?php unset($_SESSION['flash_msg']); ?>
        <?php endif; ?>

        <div class="w-full bg-surface border-2 border-primary p-8 shadow-hard">

            <div class="text-center mb-8">
                <h1 class="text-4xl font-black text-primary tracking-tighter mb-2">SCRIMSYNC</h1>
                <p class="text-secondary text-sm uppercase tracking-widest font-bold">Crear Cuenta</p>
            </div>

            <form action="controller_auth.php" method="POST" class="space-y-4">

                <input type="hidden" name="p_op" value="Registro">

                <div>
                    <label class="block text-[10px] font-black uppercase text-secondary mb-1 tracking-widest">Alias (Nick en juego)</label>
                    <input type="text" name="alias" required placeholder="Ej: Faker"
                        class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-secondary mb-1 tracking-widest">Nombre de Usuario</label>
                    <input type="text" name="username" required placeholder="Sin espacios"
                        class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-secondary mb-1 tracking-widest">Correo Electrónico</label>
                    <input type="email" name="email" required
                        class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-secondary mb-1 tracking-widest">Contraseña</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                            class="w-full bg-background border-2 border-border text-primary px-4 py-3 pr-12 font-bold text-sm focus:border-primary focus:outline-none">
                        <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary hover:text-primary transition-colors focus:outline-none">
                            <i data-lucide="eye" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary text-white py-4 font-black uppercase tracking-widest text-xs hover:bg-primary-hover transition-all mt-4 hover:translate-x-[2px] hover:translate-y-[2px] shadow-hard-sm hover:shadow-none">
                    Registrarse
                </button>
            </form>

            <div class="mt-8 text-center border-t-2 border-subtle pt-6">
                <p class="text-xs text-secondary font-bold">
                    ¿Ya tienes cuenta?
                    <a href="login.php" class="text-primary hover:underline">Inicia Sesión</a>
                </p>
            </div>
        </div>
        </div>

        <?php include '../../includes/footer.php'; ?>
