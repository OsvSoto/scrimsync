<div class="max-w-6xl mx-auto">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-black text-primary tracking-tight">Mis Equipos</h2>
            <p class="text-sm sm:text-base text-secondary mt-1">Administra tus escuadras, roles y competiciones.</p>
        </div>
        
        <a href="index.php?view=create" 
           class="w-full sm:w-auto group flex items-center justify-center sm:justify-start gap-2 px-4 py-3 sm:py-2 bg-white border border-zinc-200 rounded-lg hover:border-primary hover:shadow-md transition-all">
            <div class="bg-primary/10 text-primary p-1 rounded group-hover:bg-primary group-hover:text-white transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i>
            </div>
            <span class="text-xs font-bold uppercase tracking-widest text-zinc-700 group-hover:text-primary">Crear Equipo</span>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        
        <?php foreach ($mis_equipos as $equipo): ?>
            <?php 
                $es_capitan = ($equipo['per_elim_miembro'] == 1 || $equipo['per_modif_horario'] == 1); 
                $inicial = strtoupper(substr($equipo['equ_nombre'], 0, 1));
            ?>
            
            <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 group flex flex-col h-full">
                
                <div class="h-24 sm:h-28 bg-zinc-50 relative border-b border-zinc-100">
                    <div class="absolute top-3 left-3 z-10">
                        <span class="bg-white/90 backdrop-blur-sm border border-zinc-200 text-zinc-800 text-[10px] font-black px-2 py-1 rounded uppercase tracking-wider shadow-sm">
                            <?php echo htmlspecialchars($equipo['jue_nombre'] ?? 'General'); ?>
                        </span>
                    </div>

                    <div class="absolute top-3 right-3 z-10">
                        <?php if($es_capitan): ?>
                            <span class="bg-primary text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider shadow-sm flex items-center gap-1">
                                <i data-lucide="crown" class="w-3 h-3"></i> <span class="hidden xs:inline">Capitán</span>
                            </span>
                        <?php else: ?>
                            <span class="bg-zinc-800 text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider shadow-sm">
                                Miembro
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="absolute -bottom-6 left-6">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl border-4 border-white bg-white shadow-md overflow-hidden flex items-center justify-center">
                            <?php if($equipo['equ_logo']): ?>
                                <img src="<?php echo BASE_URL . $equipo['equ_logo']; ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-zinc-100 flex items-center justify-center text-zinc-400 font-black text-xl sm:text-2xl">
                                    <?php echo $inicial; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-6 pt-8 sm:pt-10 flex-grow flex flex-col">
                    <div class="mb-4">
                        <h3 class="font-bold text-base sm:text-lg text-zinc-900 leading-tight group-hover:text-primary transition-colors truncate">
                            <?php echo htmlspecialchars($equipo['equ_nombre']); ?>
                        </h3>
                    </div>

                    <div class="flex items-center gap-4 py-3 border-t border-b border-zinc-50 mb-6">
                        <div class="text-center flex-1">
                            <span class="block text-[10px] text-zinc-400 font-semibold uppercase">Miembros</span>
                            <span class="font-bold text-zinc-800 text-sm">-</span>
                        </div>
                        <div class="w-px h-6 bg-zinc-100"></div>
                        <div class="text-center flex-1">
                            <span class="block text-[10px] text-zinc-400 font-semibold uppercase">Scrims</span>
                            <span class="font-bold text-zinc-800 text-sm">0</span>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <div class="mt-auto">
                            <a href="../profile/view.php?id=<?php echo $equipo['equ_id']; ?>" 
                            class="w-full flex items-center justify-center gap-2 bg-zinc-900 text-white py-3 sm:py-2.5 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-primary transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Ver Equipo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>