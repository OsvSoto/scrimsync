<?php
// modules/user/profile/index.php
session_start();
require_once '../../../config/db.php';

// Seguridad: Solo usuarios logueados
if (!isset($_SESSION['loggedin'])) {
  header("Location: " . BASE_URL . "modules/auth/login.php");
  exit;
}

$usu_id = $_SESSION['usu_id'];

// Obtener datos del usuario
$sql = "SELECT * FROM usuario WHERE usu_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $usu_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

include '../../../includes/header.php';
?>

<?php include '../../../includes/user_navbar.php'; ?>
<?php include '../../../includes/user_sidebar.php'; ?>

<main class="pt-24 pb-16 px-4 sm:px-6 lg:px-8 md:ml-64 transition-all duration-300">

  <div class="max-w-4xl mx-auto space-y-8">

    <?php if (isset($_GET['msg'])): ?>
      <div class="bg-emerald-50 border-2 border-emerald-500 p-4 text-emerald-700 font-bold text-sm uppercase tracking-widest flex justify-between items-center">
        <div class="flex items-center gap-3">
          <i data-lucide="check-circle" class="w-5 h-5"></i>
          <?php
          if ($_GET['msg'] == 'profile_updated') echo "Perfil actualizado con éxito.";
          if ($_GET['msg'] == 'password_updated') echo "Contraseña actualizada con éxito.";
          ?>
        </div>
        <a href="index.php" class="hover:text-emerald-900 transition-colors">
          <i data-lucide="x" class="w-5 h-5"></i>
        </a>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
      <div class="bg-rose-50 border-2 border-rose-500 p-4 text-rose-700 font-bold text-sm uppercase tracking-widest flex justify-between items-center">
        <div class="flex items-center gap-3">
          <i data-lucide="alert-circle" class="w-5 h-5"></i>
          <?php
          if ($_GET['error'] == 'password_mismatch') echo "Las contraseñas no coinciden.";
          if ($_GET['error'] == 'invalid_current_password') echo "La contraseña actual es incorrecta.";
          if ($_GET['error'] == 'db_error') echo "Error al procesar la solicitud.";
          if ($_GET['error'] == 'file_too_large') echo "La imagen es muy grande (Máx 5MB).";
          if ($_GET['error'] == 'invalid_file_type') echo "Formato no válido. Solo JPG, PNG o WebP.";
          if ($_GET['error'] == 'image_processing_error') echo "Error al procesar la imagen.";
          ?>
        </div>
        <a href="index.php" class="hover:text-rose-900 transition-colors">
          <i data-lucide="x" class="w-5 h-5"></i>
        </a>
      </div>
    <?php endif; ?>

    <h1 class="text-3xl font-black text-zinc-950 uppercase tracking-tight">Ajustes de Perfil</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <!-- Sidebar / Avatar -->
      <div class="md:col-span-1">
        <div class="bg-white border-2 border-primary p-6 flex flex-col items-center text-center shadow-[4px_4px_0px_0px_rgba(9,9,11,1)]">
          <div class="relative mb-6 group cursor-pointer overflow-hidden rounded-sm border-2 border-primary">
            <img src="<?php echo $user['usu_foto'] ? $user['usu_foto'] : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . $user['usu_username']; ?>"
              alt="Avatar" class="w-32 h-32 object-cover">

            <!-- Overlay para subir foto -->
            <label for="photo_upload" class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
              <i data-lucide="camera" class="w-8 h-8 text-white mb-1"></i>
              <span class="text-[10px] text-white font-black uppercase tracking-widest">Cambiar</span>
            </label>
          </div>

          <h2 class="text-xl font-black text-zinc-950 uppercase tracking-tight"><?php echo htmlspecialchars($user['usu_username']); ?></h2>
          <p class="text-zinc-400 text-xs font-bold mb-6"><?php echo htmlspecialchars($user['usu_email']); ?></p>
          <span class="px-4 py-1 bg-zinc-950 text-white text-[10px] font-black uppercase tracking-widest shadow-md">
            <?php echo $user['usu_tipo'] == 0 ? 'Administrador' : 'Jugador'; ?>
          </span>
        </div>
      </div>

      <div class="md:col-span-2 space-y-6">
        <!-- Información Básica -->
        <div class="bg-white border-2 border-primary p-6 shadow-[4px_4px_0px_0px_rgba(9,9,11,1)]">
          <h3 class="text-sm font-black text-zinc-950 mb-6 flex items-center gap-2 uppercase tracking-widest">
            <i data-lucide="user" class="w-4 h-4 text-zinc-400"></i>
            Información Básica
          </h3>

          <form action="controller_profile.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="action" value="update_profile">

            <!-- Input oculto para la foto (activado por el label del avatar) -->
            <input type="file" id="photo_upload" name="usu_foto" class="hidden" accept="image/png, image/jpeg, image/webp" onchange="this.form.submit()">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-[10px] font-black uppercase text-zinc-400 mb-2 tracking-widest">Nombre de Usuario</label>
                <input type="text" name="usu_username" value="<?php echo htmlspecialchars($user['usu_username']); ?>"
                  class="w-full bg-zinc-50 border-2 border-border text-zinc-950 px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
              </div>
              <div>
                <label class="block text-[10px] font-black uppercase text-zinc-400 mb-2 tracking-widest">Alias (Nick)</label>
                <input type="text" name="usu_alias" value="<?php echo htmlspecialchars($user['usu_alias']); ?>"
                  class="w-full bg-zinc-50 border-2 border-border text-zinc-950 px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
              </div>
            </div>
            <div>
              <label class="block text-[10px] font-black uppercase text-zinc-400 mb-2 tracking-widest">Correo Electrónico</label>
              <input type="email" name="usu_email" value="<?php echo htmlspecialchars($user['usu_email']); ?>"
                class="w-full bg-zinc-50 border-2 border-border text-zinc-950 px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
            </div>
            <div>
              <label class="block text-[10px] font-black uppercase text-zinc-400 mb-2 tracking-widest">Descripción</label>
              <textarea name="usu_descripcion" rows="3"
                class="w-full bg-zinc-50 border-2 border-border text-zinc-950 px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none"><?php echo htmlspecialchars($user['usu_descripcion'] ?? ''); ?></textarea>
            </div>
            <div class="flex justify-end mt-6">
              <button type="submit" class="flex items-center gap-2 px-6 py-3 bg-zinc-950 text-white font-black text-xs uppercase tracking-widest hover:bg-zinc-800 transition-all">
                <i data-lucide="save" class="w-4 h-4"></i> Guardar Cambios
              </button>
            </div>
          </form>
        </div>

        <!-- Seguridad -->
        <div class="bg-white border-2 border-primary p-6 shadow-[4px_4px_0px_0px_rgba(9,9,11,1)]">
          <h3 class="text-sm font-black text-zinc-950 mb-6 flex items-center gap-2 uppercase tracking-widest">
            <i data-lucide="lock" class="w-4 h-4 text-zinc-400"></i>
            Seguridad
          </h3>

          <form action="controller_profile.php" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="update_password">
            <div>
              <label class="block text-[10px] font-black uppercase text-zinc-400 mb-2 tracking-widest">Contraseña Actual</label>
              <input type="password" name="current_password" required placeholder="••••••••"
                class="w-full bg-zinc-50 border-2 border-border text-zinc-950 px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-[10px] font-black uppercase text-zinc-400 mb-2 tracking-widest">Nueva Contraseña</label>
                <input type="password" name="new_password" required placeholder="••••••••"
                  class="w-full bg-zinc-50 border-2 border-border text-zinc-950 px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
              </div>
              <div>
                <label class="block text-[10px] font-black uppercase text-zinc-400 mb-2 tracking-widest">Confirmar Nueva Contraseña</label>
                <input type="password" name="confirm_password" required placeholder="••••••••"
                  class="w-full bg-zinc-50 border-2 border-border text-zinc-950 px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
              </div>
            </div>
            <div class="flex justify-end mt-6">
              <button type="submit" class="px-6 py-3 border-2 border-primary text-zinc-950 font-black text-xs uppercase tracking-widest hover:bg-zinc-50 transition-all">
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
