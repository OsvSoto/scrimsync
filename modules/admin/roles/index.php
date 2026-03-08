<?php
// modules/admin/roles/index.php
session_start();
require_once '../../../config/db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['tipo'] != 0) {
  header("Location: ../../../modules/auth/login.php");
  exit;
}

$sql = "SELECT r.rol_id, r.rol_nombre, j.jue_nombre
        FROM rol_predefinido r
        INNER JOIN juego j ON r.jue_id = j.jue_id
        ORDER BY r.rol_id DESC";
$result = mysqli_query($conn, $sql);

include '../../../includes/header.php';
?>

<?php include '../../../includes/admin_navbar.php'; ?>

<div class="min-h-screen bg-background">
  <main class="flex-1 w-full pt-16 pb-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <a href="../mantencion/index.php" class="text-secondary hover:text-primary text-xs font-bold uppercase tracking-widest mb-6 inline-block">
        <i data-lucide="arrow-left" class="w-3 h-3 inline mr-1"></i> Volver a Tablas Básicas
      </a>

      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
          <h2 class="text-3xl font-black text-primary uppercase tracking-tight mb-2">
            Juegos: Roles
          </h2>
          <p class="text-secondary text-sm">Administra los roles de juego del sistema.</p>
        </div>

        <a href="form.php" class="bg-primary text-white px-6 py-3 font-bold text-sm uppercase tracking-widest hover:bg-zinc-800 transition-colors flex items-center gap-2 self-start md:self-end">
          <i data-lucide="plus" class="w-4 h-4"></i>
          Nuevo Rol
        </a>
      </div>

      <?php if (isset($_GET['error'])): ?>
        <div class="mb-6 bg-error-light border-2 border-error-border p-4 shadow-hard-error flex items-center justify-between gap-3">
          <div class="flex items-center gap-3">
            <i data-lucide="alert-circle" class="text-error-text w-5 h-5 shrink-0"></i>
            <p class="text-error-text font-black uppercase text-xs tracking-widest leading-relaxed">
              <?php 
              if($_GET['error'] == 'dependency') echo "No se puede eliminar: existen registros vinculados a este elemento.";
              else echo "Error al procesar la solicitud."; 
              ?>
            </p>
          </div>
          <button onclick="this.parentElement.remove();" class="text-error-text hover:opacity-70 shrink-0 cursor-pointer">
            <i data-lucide="x" class="w-5 h-5"></i>
          </button>
        </div>
      <?php endif; ?>

      <div class="bg-surface border-2 border-primary overflow-hidden">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-subtle border-b-2 border-primary text-[10px] uppercase tracking-widest text-secondary">
              <th class="p-4 font-black">ID</th>
              <th class="p-4 font-black">Rol</th>
              <th class="p-4 font-black">Juego Asociado</th>
              <th class="p-4 font-black text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-subtle">
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-subtle transition-colors">
                  <td class="p-4 font-mono text-xs text-muted">#<?php echo $row['rol_id']; ?></td>
                  <td class="p-4 font-bold text-primary"><?php echo $row['rol_nombre']; ?></td>
                  <td class="p-4 text-sm text-secondary">
                    <span class="bg-subtle px-2 py-1 rounded text-xs font-bold uppercase tracking-wide">
                      <?php echo $row['jue_nombre']; ?>
                    </span>
                  </td>
                  <td class="p-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                      <a href="form.php?id=<?php echo $row['rol_id']; ?>" class="p-2 text-secondary hover:text-primary transition-colors">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                      </a>
                      <form action="controller_roles.php" method="POST" onsubmit="return confirm('¿Eliminar este rol?');">
                        <input type="hidden" name="p_op" value="E">
                        <input type="hidden" name="rol_id" value="<?php echo $row['rol_id']; ?>">
                        <button type="submit" class="p-2 text-secondary hover:text-error-text transition-colors">
                          <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="p-8 text-center text-secondary">No hay roles definidos aún.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<?php include '../../../includes/footer.php'; ?>
