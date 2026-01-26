<?php
// modules/team/profile/views/components/form_team.php
// Nota: Este archivo asume que la variable $result_juegos ya está definida en el archivo padre.
?>

<div class="bg-white border border-zinc-200 rounded-xl p-8 shadow-sm text-left">
    <h3 class="font-bold text-lg mb-6 border-b pb-4">Registrar Nuevo Equipo</h3>
    
    <form action="../actions/store_team.php" method="POST" enctype="multipart/form-data">
        <div class="space-y-6">
            
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-secondary mb-2">
                    Juego Principal
                </label>
                <div class="relative">
                    <select name="jue_id" required 
                        class="w-full bg-zinc-50 border border-zinc-200 p-3 rounded font-bold focus:outline-none focus:border-primary transition-colors appearance-none text-zinc-700">
                        <option value="" disabled selected>Selecciona un juego...</option>
                        
                        <?php 
                        // Reiniciamos el puntero del resultado por si se reutiliza la consulta
                        if(isset($result_juegos)) mysqli_data_seek($result_juegos, 0); 
                        ?>

                        <?php if (isset($result_juegos) && mysqli_num_rows($result_juegos) > 0): ?>
                            <?php while($juego = mysqli_fetch_assoc($result_juegos)): ?>
                                <option value="<?php echo $juego['jue_id']; ?>">
                                    <?php echo htmlspecialchars($juego['jue_nombre']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option value="" disabled>No hay juegos registrados</option>
                        <?php endif; ?>
                        
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-zinc-700">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-secondary mb-2">
                    Nombre del Equipo
                </label>
                <input type="text" name="nombre" required placeholder="Ej: ScrimSync Esports"
                    class="w-full bg-zinc-50 border border-zinc-200 p-3 rounded font-bold focus:outline-none focus:border-primary transition-colors">
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-secondary mb-2">
                        Logo
                    </label>
                    <input type="file" name="logo" accept="image/*"
                        class="w-full text-xs file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-zinc-800">
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t flex justify-end">
            <button type="submit" 
                class="bg-primary text-white px-8 py-3 rounded font-bold uppercase tracking-widest hover:bg-zinc-800 transition-colors shadow-lg shadow-zinc-200">
                Crear Equipo
            </button>
        </div>
    </form>
</div>