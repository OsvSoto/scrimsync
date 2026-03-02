<?php
session_start();
require_once '../../../config/db.php';
include '../../../includes/header.php';

$p_op = 'C';
$titulo = "Nuevo Juego";
$jue_id = '';
$jue_nombre = '';
$current_gen_id = '';

$sql_generos = "SELECT * FROM genero ORDER BY gen_nombre ASC";
$result_generos = $conn->query($sql_generos);

if (isset($_GET['id'])) {
    $p_op = 'M';
    $titulo = "Modificar Juego";
    $id = $_GET['id'];
    $sql = "SELECT jue_id, jue_nombre, gen_id FROM juego WHERE jue_id = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = $result->fetch_assoc()) {
            $jue_id = $row['jue_id'];
            $jue_nombre = $row['jue_nombre'];
            $current_gen_id = $row['gen_id'];
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
                <form action="controller_juego.php" method="POST" class="space-y-6">
                    <input type="hidden" name="p_op" value="<?php echo $p_op; ?>">
                    <?php if ($p_op == 'M'): ?>
                        <input type="hidden" name="jue_id" value="<?php echo $jue_id; ?>">
                    <?php endif; ?>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-secondary mb-2 tracking-widest">Nombre del Juego</label>
                        <input type="text" name="jue_nombre" value="<?php echo htmlspecialchars($jue_nombre); ?>" required
                               class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-secondary mb-2 tracking-widest">Género</label>
                        <div class="relative">
                            <select name="gen_id" required class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none appearance-none">
                                <option value="" disabled <?php echo empty($current_gen_id) ? 'selected' : ''; ?>>Seleccione un género...</option>
                                <?php
                                if($result_generos->num_rows > 0) {
                                    $result_generos->data_seek(0);
                                    while($gen = $result_generos->fetch_assoc()):
                                        $selected = ($gen['gen_id'] == $current_gen_id) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $gen['gen_id']; ?>" <?php echo $selected; ?>>
                                        <?php echo $gen['gen_nombre']; ?>
                                    </option>
                                <?php endwhile; } ?>
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
