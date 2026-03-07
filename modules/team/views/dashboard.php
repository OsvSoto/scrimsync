<div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-8">
    <div>
        <h2 class="text-2xl sm:text-3xl font-black text-primary uppercase tracking-tight mb-2">
            Mis Equipos
        </h2>
        <p class="text-secondary text-sm font-medium uppercase tracking-widest">
            Administra tus equipos.
        </p>
    </div>

    <a href="index.php?view=create"
        class="w-full sm:w-auto bg-primary text-white border-2 border-primary px-6 py-3 font-black text-xs uppercase tracking-widest hover:bg-primary-hover  transition-all flex items-center justify-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Crear Equipo</span>
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

    <?php foreach ($mis_equipos as $equipo): ?>
        <?php
        $es_capitan = $equipo['per_elim_miembro'];
        $inicial = strtoupper(substr($equipo['equ_nombre'], 0, 1));
        ?>

        <a href="../profile/view.php?id=<?php echo $equipo['equ_id']; ?>"
            class="bg-surface border-2 border-primary shadow-hard-sm hover:shadow-hard  transition-all group flex flex-col h-full block">

            <div class="h-24 sm:h-28 bg-subtle relative border-b-2 border-primary">
                <div class="absolute top-3 left-3 z-10">
                    <span class="bg-surface border-2 border-primary text-primary text-[10px] font-black px-2 py-1 uppercase tracking-wider">
                        <?php echo htmlspecialchars($equipo['jue_nombre'] ?? 'General'); ?>
                    </span>
                </div>

                <div class="absolute top-3 right-3 z-10">
                    <?php if ($es_capitan): ?>
                        <span class="bg-primary text-white border-2 border-primary text-[10px] font-black px-2 py-1 uppercase tracking-wider flex items-center gap-1">
                            <i data-lucide="crown" class="w-3 h-3"></i> <span class="hidden xs:inline">Capitán</span>
                        </span>
                    <?php else: ?>
                        <span class="bg-primary-hover text-white border-2 border-primary-hover text-[10px] font-black px-2 py-1 uppercase tracking-wider">
                            Miembro
                        </span>
                    <?php endif; ?>
                </div>

                <div class="absolute -bottom-6 left-6">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 border-2 border-primary bg-surface shadow-sm overflow-hidden flex items-center justify-center">
                        <?php if ($equipo['equ_logo']): ?>
                            <img src="<?php echo "../../../" . $equipo['equ_logo']; ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-subtle flex items-center justify-center text-muted font-black text-xl sm:text-2xl">
                                <?php echo $inicial; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="p-5 sm:p-6 pt-8 sm:pt-10 flex-grow flex flex-col">
                <div class="mb-4">
                    <h3 class="font-black text-base sm:text-lg text-primary leading-tight uppercase tracking-tight truncate group-hover:text-primary-hover transition-colors">
                        <?php echo htmlspecialchars($equipo['equ_nombre']); ?>
                    </h3>
                </div>

                <div class="flex items-center gap-4 py-3 border-t-2 border-subtle mb-6">
                    <div class="text-center flex-1">
                        <span class="block text-[10px] text-muted font-black uppercase tracking-widest">Miembros</span>
                        <span class="font-black text-primary text-sm"><?php echo isset($member_counts[$equipo['equ_id']]) ? $member_counts[$equipo['equ_id']] : 0; ?></span>
                    </div>
                    <div class="w-px h-6 bg-border"></div>
                    <div class="text-center flex-1">
                        <span class="block text-[10px] text-muted font-black uppercase tracking-widest">Capitan</span>
                        <span class="font-black text-primary text-sm truncate block max-w-[80px]"><?php echo htmlspecialchars($equipo['capitan'] ?? 'N/A'); ?></span>
                    </div>
                </div>

                <div class="mt-auto">
                    <div class="w-full flex items-center justify-center gap-2 bg-primary text-white border-2 border-primary py-3 sm:py-2.5 text-xs font-black uppercase tracking-widest group-hover:bg-primary-hover group-hover:border-primary-hover transition-colors">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                        Ver Equipo
                    </div>
                </div>
            </div>
        </a>
    <?php endforeach; ?>

</div>
