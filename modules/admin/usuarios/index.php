<?php
// modules/admin/usuarios/index.php
session_start();
require_once '../../../config/db.php';

// Validar Admin
if (!isset($_SESSION['loggedin']) || $_SESSION['tipo'] != 0) {
  header("Location: ../../../modules/auth/login.php");
  exit;
}

// Búsqueda
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$sql = "SELECT * FROM usuario WHERE usu_tipo = 1";
$params = [];

if (!empty($search)) {
  $sql .= " AND usu_username LIKE ?";
  $params[] = "%$search%";
}

$sql .= " ORDER BY usu_id DESC";
$result = $conn->execute_query($sql, $params);

include '../../../includes/header.php';
?>

<?php include '../../../includes/admin_navbar.php'; ?>

<div class="min-h-screen bg-background">
  <main class="flex-1 w-full pt-16 pb-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <a href="../dashboard.php" class="text-secondary hover:text-primary text-xs font-bold uppercase tracking-widest mb-6 inline-block">
        <i data-lucide="arrow-left" class="w-3 h-3 inline mr-1"></i> Volver al Dashboard
      </a>

      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
          <h2 class="text-3xl font-black text-primary uppercase tracking-tight mb-2">
            Gestión de Usuarios
          </h2>
          <p class="text-secondary text-sm">Administra los permisos de los usuarios registrados.</p>
        </div>

        <form action="" method="GET" class="flex gap-2 w-full md:w-auto">
          <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Buscar por usuario..."
            class="text-primary bg-surface border-2 border-border p-2.5 text-sm hover:border-primary focus:border-primary outline-none w-full md:w-64 shadow-hard-sm hover:shadow-hard focus:shadow-hard transition-all">
          <button type="submit" class="bg-primary text-white p-2.5 hover:bg-primary-hover transition-all shadow-hard-sm hover:shadow-hard hover:border-primary cursor-pointer">
            <i data-lucide="search" class="w-5 h-5"></i>
          </button>
          <?php if (!empty($search)): ?>
            <a href="index.php" class="bg-subtle text-secondary p-2 hover:bg-border transition-colors" title="Limpiar búsqueda">
              <i data-lucide="x" class="w-5 h-5"></i>
            </a>
          <?php endif; ?>
        </form>
      </div>

      <?php if (isset($_SESSION['flash_msg'])): ?>
        <div class="mb-6 bg-success-light border-2 border-success-border p-4 shadow-hard-success flex items-start justify-between gap-3">
          <div class="flex items-start gap-3">
            <i data-lucide="check-circle" class="text-success-text w-5 h-5 shrink-0 mt-0.5"></i>
            <p class="text-success-text font-black uppercase text-xs tracking-widest leading-relaxed">
              <?php
              if ($_SESSION['flash_msg'] == 'promovido') echo "Usuario promovido a administrador exitosamente";
              ?>
            </p>
          </div>
          <button onclick="this.parentElement.remove();" class="text-success-text hover:opacity-70 shrink-0">
            <i data-lucide="x" class="w-5 h-5"></i>
          </button>
        </div>
        <?php unset($_SESSION['flash_msg']); ?>
      <?php endif; ?>

      <div class="bg-surface border-2 border-primary overflow-hidden flex flex-col relative shadow-hard-sm" style="max-height: 600px;">
        <div class="overflow-y-auto w-full">
          <table class="w-full text-left border-collapse table-fixed">
            <thead class="sticky top-0 z-10 bg-subtle border-b-2 border-primary text-[10px] uppercase tracking-widest text-secondary shadow-sm">
              <tr>
                <th class="p-4 font-black w-20">ID</th>
                <th class="p-4 font-black w-90">Usuario</th>
                <th class="p-4 font-black w-55">Email</th>
                <th class="p-4 font-black w-auto text-right">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-subtle">
              <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr class="hover:bg-subtle transition-colors">
                    <td class="p-4 font-mono text-xs text-muted truncate">#<?php echo $row['usu_id']; ?></td>
                    <td class="p-4">
                      <div class="flex items-center gap-3 font-bold text-primary truncate">
                        <?php if ($row['usu_foto']): ?>
                          <img src="<?php echo $row['usu_foto']; ?>" class="w-8 h-8 rounded-full object-cover shrink-0">
                        <?php else: ?>
                          <div class="w-8 h-8 rounded-full bg-border flex items-center justify-center shrink-0">
                            <i data-lucide="user" class="w-4 h-4 text-muted"></i>
                          </div>
                        <?php endif; ?>
                        <div class="truncate">
                          <?php echo $row['usu_username']; ?>
                          <span class="text-xs text-secondary font-normal block md:inline">(<?php echo $row['usu_alias']; ?>)</span>
                        </div>
                      </div>
                    </td>
                    <td class="p-4 text-sm font-medium text-secondary truncate"><?php echo $row['usu_email']; ?></td>
                    <td class="p-4 text-right">
                      <form action="controller_usuarios.php" method="POST" onsubmit="return confirm('¿Estás seguro de promover a <?php echo $row['usu_username']; ?> a Administrador? Esta acción otorgará control total sobre el sistema.');">
                        <input type="hidden" name="p_op" value="Asignar_Tipo_Usu">
                        <input type="hidden" name="usu_id" value="<?php echo $row['usu_id']; ?>">
                        <button type="submit" class="bg-primary text-white px-3 py-2 text-xs font-bold uppercase tracking-wider hover:bg-success-border transition-colors inline-flex items-center gap-2 cursor-pointer">
                          <i data-lucide="shield-check" class="w-3 h-3"></i>
                          <span class="hidden lg:inline">Hacer Admin</span>
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="p-8 text-center text-secondary">
                    <?php echo !empty($search) ? 'No se encontraron usuarios.' : 'No hay usuarios elegibles para promover.'; ?>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>


<?php include '../../../includes/footer.php'; ?>
