<?php
// modules/team/profile/views/components/form_team.php
?>

<div class="bg-surface border-2 border-primary p-8 shadow-hard text-left">
  <h3 class="font-black text-lg text-primary mb-6 border-b-2 border-subtle pb-4 uppercase tracking-tight">Registrar Nuevo Equipo</h3>

  <form action="../actions/create_team.php" method="POST" enctype="multipart/form-data">
    <div class="space-y-6">

      <div>
        <label class="block text-[10px] font-black uppercase tracking-widest text-secondary mb-2">
          Juego Principal
        </label>
        <div class="relative">
          <select name="jue_id" required
            class="w-full bg-subtle border-2 border-border p-3 font-bold text-sm focus:outline-none focus:border-primary transition-colors appearance-none text-primary">
            <option value="" disabled selected>Selecciona un juego...</option>

            <?php
            if (isset($result_juegos)) $result_juegos->data_seek(0);
            ?>

            <?php if (isset($result_juegos) && $result_juegos->num_rows > 0): ?>
              <?php while ($juego = $result_juegos->fetch_assoc()): ?>
                <option value="<?php echo $juego['jue_id']; ?>">
                  <?php echo htmlspecialchars($juego['jue_nombre']); ?>
                </option>
              <?php endwhile; ?>
            <?php else: ?>
              <option value="" disabled>No hay juegos registrados</option>
            <?php endif; ?>

          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-secondary">
            <i data-lucide="chevron-down" class="w-4 h-4"></i>
          </div>
        </div>
      </div>

      <div>
        <label class="block text-[10px] font-black uppercase tracking-widest text-secondary mb-2">
          Nombre del Equipo
        </label>
        <input type="text" name="nombre" required placeholder="Ej: ScrimSync Esports"
          class="w-full bg-subtle border-2 border-border p-3 font-bold text-sm focus:outline-none focus:border-primary transition-colors text-primary">
      </div>

      <div class="grid grid-cols-2 gap-6">
        <div>
          <label class="block text-[10px] font-black uppercase tracking-widest text-secondary mb-2">
            Logo
          </label>
          <input type="file" name="logo" accept="image/*"
            class="w-full text-xs file:mr-4 file:py-2 file:px-4 file:rounded-none file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-primary file:text-white hover:file:bg-primary-hover transition-all">
        </div>
      </div>
    </div>

    <div class="mt-8 pt-6 border-t-2 border-subtle flex justify-end">
      <button type="submit"
        class="bg-primary text-white px-8 py-4 font-black text-xs uppercase tracking-widest hover:bg-primary-hover transition-all shadow-hard hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px]">
        Crear Equipo
      </button>
    </div>
  </form>
</div>
