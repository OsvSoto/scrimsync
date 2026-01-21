<?php
// modules/admin/genero/form.php
session_start();
require_once '../../../config/db.php';
include '../../../includes/header.php';

// Variables por defecto (Modo Crear)
$p_op = 'C';
$titulo = "Nuevo Género";
$gen_id = '';
$gen_nombre = '';

// 2. MODO EDICIÓN
if (isset($_GET['id'])) {
    $p_op = 'M';
    $titulo = "Modificar Género";
    $id = $_GET['id'];

    $sql = "SELECT gen_id, gen_nombre FROM genero WHERE gen_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $gen_id = $row['gen_id'];
            $gen_nombre = $row['gen_nombre'];
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<?php include '../../../includes/admin_navbar.php'; ?>

<div class="min-h-screen bg-background flex pt-16">

    <?php include '../../../includes/admin_sidebar.php'; ?>

    <main class="w-full md:ml-64 p-8 flex justify-center">
        <div class="w-full max-w-lg">

            <a href="index.php" class="text-secondary hover:text-primary text-xs font-bold uppercase tracking-widest mb-6 inline-block">
                <i data-lucide="arrow-left" class="w-3 h-3 inline mr-1"></i> Volver al listado
            </a>

            <div class="bg-surface border-2 border-primary p-8 shadow-[4px_4px_0px_0px_rgba(9,9,11,1)]">
                <h2 class="text-2xl font-black text-primary uppercase tracking-tight mb-8">
                    <?php echo $titulo; ?>
                </h2>

                <form action="controller_genero.php" method="POST" class="space-y-6">
                    <input type="hidden" name="p_op" value="<?php echo $p_op; ?>">
                    <?php if ($p_op == 'M'): ?>
                        <input type="hidden" name="gen_id" value="<?php echo $gen_id; ?>">
                    <?php endif; ?>

                    <div>
                        <label class="block text-[10px] font-black uppercase text-secondary mb-2 tracking-widest">Nombre del Género</label>
                        <input type="text" name="gen_nombre" value="<?php echo htmlspecialchars($gen_nombre); ?>" required
                               class="w-full bg-background border-2 border-border text-primary px-4 py-3 font-bold text-sm focus:border-primary focus:outline-none">
                    </div>

                    <button type="submit" class="w-full bg-primary text-white py-4 font-black uppercase tracking-widest text-xs hover:bg-zinc-800 transition-all">
                        Guardar Cambios
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include '../../../includes/footer.php'; ?>
