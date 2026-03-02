<?php
// modules/admin/roles/form.php
session_start();
require_once '../../../config/db.php';
include '../../../includes/header.php';

$p_op = 'C';
$titulo = "Nuevo Rol";
$rol_id = '';
$rol_nombre = '';
$current_jue_id = '';

$sql_juegos = "SELECT * FROM juego ORDER BY jue_nombre ASC";
$result_juegos = mysqli_query($conn, $sql_juegos);

if (isset($_GET['id'])) {
  $p_op = 'M';
  $titulo = "Modificar Rol";
  $id = $_GET['id'];

  $sql = "SELECT rol_id, rol_nombre, jue_id FROM rol_predefinido WHERE rol_id = ?";
  if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = $result->fetch_assoc()) {
      $rol_id = $row['rol_id'];
      $rol_nombre = $row['rol_nombre'];
      $current_jue_id = $row['jue_id'];
    }
    mysqli_stmt_close($stmt);
  }
}
?>

<?php include '../../../includes/admin_navbar.php'; ?>

<div class="min-h-screen bg-background flex flex-col">

  <main class="flex-1 w-full pt-16 pb-8 flex flex-col items-center">
    <div class="w-full max-w-lg px-4 sm:px-6 lg:px-8">

      <a href="index.php" class="text-secondary hover:text-primary text-xs font-bold uppercase tracking-widest mb-4 inline-block">
        <i data-lucide="arrow-left" class="w-3 h-3 inline mr-1"></i> Volver al listado
      </a>

      <div class="bg-surface border-2 border-primary p-8 shadow-hard">
        <h2 class="text-2xl font-black text-primary uppercase tracking-tight mb-8">
          <?php echo $titulo; ?>
        </h2>

        <form action="controller_roles.php" method="POST" class="space-y-6">
          <input type="hidden" name="p_op" value="<?php echo $p_op; ?>">
          <?php if ($p_op == 'M'): ?>
            <input type="hidden" name="rol_id" value="<?php echo $rol_id; ?>">
          <?php endif; ?>

          <div>
            <label class="block text-[10px] font-black uppercase text-secondary mb-2 tracking-widest">Nombre del Rol</label>
            <input type="text" name="rol_nombre" value="<?php echo htmlspecialchars($rol_nombre); ?>" required
              class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
          </div>

          <div>
            <label class="block text-[10px] font-black uppercase text-secondary mb-2 tracking-widest">Juego Asociado</label>
            <div class="relative">
              <select name="jue_id" required class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none appearance-none">
                <option value="" disabled <?php echo empty($current_jue_id) ? 'selected' : ''; ?>>Seleccione un juego...</option>
                <?php
                if ($result_juegos->num_rows > 0) {
                  $result_juegos->data_seek(0);
                  while ($jue = $result_juegos->fetch_assoc()):
                    $selected = ($jue['jue_id'] == $current_jue_id) ? 'selected' : '';
                ?>
                    <option value="<?php echo $jue['jue_id']; ?>" <?php echo $selected; ?>>
                      <?php echo $jue['jue_nombre']; ?>
                    </option>
                <?php endwhile;
                } ?>
              </select>
              <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-secondary pointer-events-none"></i>
            </div>
          </div>

          <button type="submit" class="w-full bg-primary text-white py-4 font-black uppercase tracking-widest text-xs hover:bg-primary-hover transition-all shadow-hard hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] cursor-pointer">
            Guardar Cambios
          </button>
        </form>
      </div>
    </div>
  </main>
</div>

<?php include '../../../includes/footer.php'; ?>
