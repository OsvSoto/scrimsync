<?php
// CONSULTA PARA OBTENER LOS JUEGOS DISPONIBLES
// Esta consulta es necesaria para que el componente funcione
$sql_juegos = "SELECT * FROM juego ORDER BY jue_nombre ASC";
$result_juegos = mysqli_query($conn, $sql_juegos);
?>

<div class="max-w-2xl mx-auto text-center mt-10">
    <div class="bg-white border-2 border-primary p-4 w-20 h-20 mx-auto flex items-center justify-center mb-6 shadow-[4px_4px_0px_0px_rgba(9,9,11,1)]">
        <i data-lucide="shield-alert" class="w-10 h-10 text-primary"></i>
    </div>

    <h2 class="text-3xl font-black text-primary uppercase tracking-tight mb-4">
        Aún no tienes equipo
    </h2>
    <p class="text-secondary font-bold text-sm mb-10 max-w-md mx-auto">
        Para competir en torneos y buscar scrims, necesitas formar parte de una organización o crear la tuya propia.
    </p>

    <?php include 'components/form_teams.php'; ?>

</div>