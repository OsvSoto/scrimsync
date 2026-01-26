<?php
// CONSULTA PARA OBTENER LOS JUEGOS DISPONIBLES
// Esta consulta es necesaria para que el componente funcione
$sql_juegos = "SELECT * FROM juego ORDER BY jue_nombre ASC";
$result_juegos = mysqli_query($conn, $sql_juegos);
?>

<div class="max-w-2xl mx-auto text-center mt-10">
    <div class="bg-zinc-100 p-4 rounded-full w-20 h-20 mx-auto flex items-center justify-center mb-6">
        <i data-lucide="shield-alert" class="w-10 h-10 text-zinc-400"></i>
    </div>

    <h2 class="text-3xl font-black text-primary tracking-tight mb-4">
        Aún no tienes equipo
    </h2>
    <p class="text-secondary mb-10 max-w-md mx-auto">
        Para competir en torneos y buscar scrims, necesitas formar parte de una organización o crear la tuya propia.
    </p>

    <?php include 'components/form_teams.php'; ?>

</div>