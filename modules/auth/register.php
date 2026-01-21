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
    <div class="max-w-md w-full bg-surface border-2 border-primary p-8 shadow-[8px_8px_0px_0px_rgba(9,9,11,1)]">
        
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black text-primary tracking-tighter mb-2">SCRIMSYNC</h1>
            <p class="text-secondary text-sm uppercase tracking-widest font-bold">Crear Cuenta</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="mb-6 bg-rose-100 border-2 border-rose-500 text-rose-700 p-3 text-xs font-bold uppercase tracking-wide">
                <?php 
                    if ($_GET['error'] == 'db_error') echo "Error al registrar. El usuario o email ya existen.";
                    else echo "Ocurrió un error inesperado.";
                ?>
            </div>
        <?php endif; ?>

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
                <input type="password" name="password" required 
                       class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
            </div>

            <button type="submit" class="w-full bg-primary text-white py-4 font-black uppercase tracking-widest text-xs hover:bg-zinc-800 transition-all mt-4 hover:translate-x-[2px] hover:translate-y-[2px] shadow-[4px_4px_0px_0px_rgba(0,0,0,0.2)] hover:shadow-none">
                Registrarse
            </button>
        </form>

        <div class="mt-8 text-center border-t-2 border-zinc-100 pt-6">
            <p class="text-xs text-secondary font-bold">
                ¿Ya tienes cuenta? 
                <a href="login.php" class="text-primary hover:underline">Inicia Sesión</a>
            </p>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>