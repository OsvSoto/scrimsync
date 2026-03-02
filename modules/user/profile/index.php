<?php
// modules/user/profile/index.php
session_start();
require_once '../../../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../../../modules/auth/login.php");
    exit;
}

$usu_id = $_SESSION['usu_id'];

$sql = "SELECT * FROM usuario WHERE usu_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $usu_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

include '../../../includes/header.php';
?>

<?php
if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 0) {
    include "../../../includes/admin_navbar.php";
} else {
    include '../../../includes/user_navbar.php';
}
?>
<?php # include '../../../includes/user_sidebar.php';
?>

<main class="max-w-6xl mx-auto pt-16 pb-16 px-4 sm:px-6 lg:px-8 transition-all duration-300">
    <div class="space-y-8">
        <?php if (isset($_SESSION['flash_msg'])): ?>
            <div class="mb-6 bg-success-light border-2 border-success-border p-4 shadow-hard-success flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <i data-lucide="check-circle" class="text-success-text w-5 h-5 shrink-0 mt-0.5"></i>
                    <p class="text-success-text font-black uppercase text-xs tracking-widest leading-relaxed">
                        <?php
                        switch ($_SESSION['flash_msg']) {
                            case 'profile_updated':
                                echo "Perfil actualizado con éxito.";
                                break;
                            case 'password_updated':
                                echo "Contraseña actualizada con éxito.";
                                break;
                        }
                        ?>
                    </p>
                </div>
                <button onclick="this.parentElement.remove();" class="text-success-text hover:opacity-70 shrink-0 cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <?php unset($_SESSION['flash_msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="mb-6 bg-error-light border-2 border-error-border p-4 shadow-hard-error flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <i data-lucide="alert-circle" class="text-error-text w-5 h-5 shrink-0 mt-0.5"></i>
                    <p class="text-error-text font-black uppercase text-xs tracking-widest leading-relaxed">
                        <?php
                        switch ($_SESSION['flash_error']) {
                            case 'password_mismatch':
                                echo "Las contraseñas no coinciden.";
                                break;
                            case 'invalid_current_password':
                                echo "La contraseña actual es incorrecta.";
                                break;
                            case 'db_error':
                                echo "Error al procesar la solicitud.";
                                break;
                            case 'file_too_large':
                                echo "La imagen es muy grande (Máx 5MB).";
                                break;
                            case 'invalid_file_type':
                                echo "Formato no válido. Solo JPG, PNG o WebP.";
                                break;
                            case 'image_processing_error':
                                echo "Error al procesar la imagen.";
                                break;
                        }
                        ?>
                    </p>
                </div>
                <button onclick="this.parentElement.remove();" class="text-error-text hover:opacity-70 shrink-0">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <h1 class="text-3xl font-black text-primary uppercase tracking-tight">Ajustes de Perfil</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-surface border-2 border-primary p-6 flex flex-col items-center text-center shadow-hard">
                    <div class="relative mb-6 group cursor-pointer overflow-hidden rounded-sm border-2 border-primary">
                        <img src="<?php echo $user['usu_foto'] ? $user['usu_foto'] : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . $user['usu_username']; ?>"
                            alt="Avatar" class="w-32 h-32 object-cover">

                        <label for="photo_upload" class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                            <i data-lucide="camera" class="w-8 h-8 text-white mb-1"></i>
                            <span class="text-[10px] text-white font-black uppercase tracking-widest">Cambiar</span>
                        </label>
                    </div>

                    <h2 class="text-xl font-black text-primary uppercase tracking-tight"><?php echo htmlspecialchars($user['usu_username']); ?></h2>
                    <p class="text-muted text-xs font-bold mb-6"><?php echo htmlspecialchars($user['usu_email']); ?></p>
                    <span class="px-4 py-1 bg-primary text-white text-[10px] font-black uppercase tracking-widest shadow-hard-sm">
                        <?php echo $user['usu_tipo'] == 0 ? 'Administrador' : 'Jugador'; ?>
                    </span>
                </div>
            </div>

            <div class="md:col-span-2 space-y-6">
                <div class="bg-surface border-2 border-primary p-6 shadow-hard">
                    <h3 class="text-sm font-black text-primary mb-6 flex items-center gap-2 uppercase tracking-widest">
                        <i data-lucide="user" class="w-4 h-4 text-muted"></i>
                        Información Básica
                    </h3>

                    <form action="controller_profile.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="action" value="update_profile">

                        <input type="file" id="photo_upload" name="usu_foto" class="hidden" accept="image/png, image/jpeg, image/webp" onchange="this.form.submit()">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-muted mb-2 tracking-widest">Nombre de Usuario</label>
                                <input type="text" name="usu_username" value="<?php echo htmlspecialchars($user['usu_username']); ?>"
                                    class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-muted mb-2 tracking-widest">Alias (Nick)</label>
                                <input type="text" name="usu_alias" value="<?php echo htmlspecialchars($user['usu_alias']); ?>"
                                    class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-muted mb-2 tracking-widest">Correo Electrónico</label>
                            <input type="email" name="usu_email" value="<?php echo htmlspecialchars($user['usu_email']); ?>"
                                class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-muted mb-2 tracking-widest">Descripción</label>
                            <textarea name="usu_descripcion" rows="3"
                                class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none"><?php echo htmlspecialchars($user['usu_descripcion'] ?? ''); ?></textarea>
                        </div>
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="flex items-center gap-2 px-6 py-3 bg-primary text-white font-black text-xs uppercase tracking-widest hover:bg-primary-hover transition-all shadow-hard hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] cursor-pointer">
                                <i data-lucide="save" class="w-4 h-4"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-surface border-2 border-primary p-6 shadow-hard">
                    <h3 class="text-sm font-black text-primary mb-6 flex items-center gap-2 uppercase tracking-widest">
                        <i data-lucide="lock" class="w-4 h-4 text-muted"></i>
                        Seguridad
                    </h3>

                    <form action="controller_profile.php" method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="update_password">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-muted mb-2 tracking-widest">Contraseña Actual</label>
                            <input type="password" name="current_password" required placeholder="••••••••"
                                class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-muted mb-2 tracking-widest">Nueva Contraseña</label>
                                <input type="password" name="new_password" required placeholder="••••••••"
                                    class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-muted mb-2 tracking-widest">Confirmar Nueva Contraseña</label>
                                <input type="password" name="confirm_password" required placeholder="••••••••"
                                    class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
                            </div>
                        </div>
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="px-6 py-3 border-2 border-primary text-primary font-black text-xs uppercase tracking-widest hover:bg-subtle transition-all shadow-hard hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] cursor-pointer">
                                Actualizar Credenciales
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../../../includes/footer.php'; ?>
