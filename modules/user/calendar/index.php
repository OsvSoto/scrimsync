<?php
// modules/user/calendar/index.php
require_once 'controller_calendar.php';
include '../../../includes/header.php';
?>

<?php include '../../../includes/user_navbar.php'; ?>

<div class="flex min-h-screen bg-background">
    <main class="w-full pt-16 pb-8 transition-all duration-300">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if (isset($_SESSION['flash_msg'])): ?>
                <div class="mb-6 bg-emerald-50 border-2 border-emerald-500 p-4 text-emerald-700 font-bold text-sm uppercase tracking-widest flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <?php
                        if ($_SESSION['flash_msg'] == 'scrim_cancelled')
                            echo 'Scrim cancelado correctamente.';
                        ?>
                    </div>
                    <i data-lucide="x" onclick="return this.parentNode.remove();" class="inline w-5 h-4 fill-current ml-2 hover:opacity-80 cursor-pointer"></i>
                </div>
                <?php unset($_SESSION['flash_msg']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="mb-6 p-4 bg-error-light border-2 border-error-border text-error-text font-bold shadow-hard-error flex items-center gap-2 rounded-none">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    <span>
                        <?php
                        if ($_SESSION['flash_error'] == 'not_authorized')
                            echo 'No tienes permisos para realizar esta acción.';
                        ?>
                    </span>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-surface border-2 border-primary p-4 shadow-hard">
                <div class="flex items-center gap-4">
                    <a href="?month=<?php echo $month - 1; ?>&year=<?php echo $year; ?>"
                        class="p-2 hover:bg-subtle rounded-sm text-muted hover:text-primary transition-colors">
                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                    </a>
                    <h2 class="text-xl font-black text-primary w-48 text-center uppercase tracking-tight">
                        <?php
                        $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                        echo $meses[$month] . ' ' . $year;
                        ?>
                    </h2>
                    <a href="?month=<?php echo $month + 1; ?>&year=<?php echo $year; ?>"
                        class="p-2 hover:bg-subtle rounded-sm text-muted hover:text-primary transition-colors">
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </a>
                </div>

                <div class="flex flex-wrap justify-center gap-x-4 gap-y-2 text-[10px] md:text-xs font-bold uppercase tracking-wider">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-emerald-500 rounded-none"></div>
                        <span class="text-secondary">Confirmadas</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-amber-500 rounded-none"></div>
                        <span class="text-secondary">Pendientes</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-rose-500 rounded-none"></div>
                        <span class="text-secondary">Canceladas</span>
                    </div>
                </div>
            </div>

            <div class="w-full mt-8">
                <!-- version pc -->
                <div class="hidden md:block">
                    <div class="grid grid-cols-7 mb-2">
                        <?php
                        $dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                        foreach ($dias as $dia):
                        ?>
                            <div class="text-muted text-center text-[10px] font-black uppercase tracking-widest py-2">
                                <?php echo $dia; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="grid grid-cols-7 gap-px bg-border border-2 border-primary shadow-hard-sm overflow-hidden">
                        <?php for ($i = 0; $i < $dayOfWeek; $i++) {
                            echo '<div class="bg-subtle min-h-25 border border-border"></div>';
                        }
                        for ($day = 1; $day <= $numberDays; $day++) {
                            $currentDate = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                            $isToday = ($day == date('j') && $month == date('n') && $year == date('Y'));
                            $dayScrims = array_filter($scrims, function ($s) use ($day, $month, $year) {
                                $d = strtotime($s['scr_fecha_juego']);
                                return date('j', $d) == $day && date('n', $d) == $month && date('Y', $d) == $year;
                            }); ?>
                            <div class="bg-surface border border-border min-h-35 p-2 relative group hover:bg-subtle transition-colors <?php echo $isToday ? 'ring-1 ring-inset ring-primary' : ''; ?>">
                                <div class="text-sm font-bold mb-2 <?php echo $isToday ? 'text-primary' : 'text-muted'; ?>"><?php echo $day; ?></div>

                                <div class="grid gap-2">
                                    <?php foreach ($dayScrims as $scrim): ?>
                                        <?php
                                        $statusClass = 'bg-subtle text-secondary';
                                        $dotClass = 'bg-muted';
                                        $desc = strtolower($scrim['est_descripcion']);
                                        $is_cancelled = (str_contains($desc, 'cancel'));
                                        $is_accepted = (str_contains($desc, 'confirm') || str_contains($desc, 'aceptad'));

                                        if ($is_accepted) {
                                            $statusClass = 'bg-success-light text-success-text border border-emerald-100';
                                            $dotClass = 'bg-emerald-500';
                                        } elseif (str_contains($desc, 'pendient')) {
                                            $statusClass = 'bg-amber-50 text-amber-700 border border-amber-100';
                                            $dotClass = 'bg-amber-500';
                                        } elseif ($is_cancelled) {
                                            $statusClass = 'bg-error-light text-error-text border border-rose-100 line-through';
                                            $dotClass = 'bg-rose-500';
                                        }
                                        ?>
                                        <div class="flex items-center justify-between gap-1 p-1.5 rounded-none text-[10px] md:text-xs font-bold transition-colors <?php echo $statusClass; ?> <?php echo $is_accepted ? 'cursor-pointer' : ''; ?>">
                                            <button
                                                type="button"
                                                <?php if ($is_accepted): ?>
                                                onclick="document.getElementById('scrim_modal_<?php echo $scrim['scr_id']; ?>').showModal()"
                                                <?php endif; ?>
                                                class="flex flex-col gap-0.5 leading-tight text-left flex-1 truncate"
                                                title="<?php echo htmlspecialchars($scrim['opponent_name']) . ' @ ' . substr($scrim['scr_hora_inicio'], 0, 5); ?>">

                                                <span class="text-[9px] font-medium opacity-70 truncate">
                                                    <?php echo htmlspecialchars(substr($scrim['scr_hora_inicio'], 0, 5)); ?>
                                                </span>

                                                <div class="flex items-center gap-1.5 truncate">
                                                    <div class="shrink-0 w-1.5 h-1.5 <?php echo $dotClass; ?>"></div>
                                                    <span class="truncate font-semibold uppercase">
                                                        <?php echo htmlspecialchars($scrim['opponent_name']); ?>
                                                    </span>
                                                </div>
                                            </button>

                                            <?php if ($is_accepted && $is_captain): ?>
                                                <form action="../../scrim/controller_scrim.php" method="POST"
                                                    onsubmit="event.stopPropagation(); return confirm('¿ESTÁS SEGURO?');"
                                                    class="opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                                    <input type="hidden" name="action" value="cancelar">
                                                    <input type="hidden" name="scr_id" value="<?php echo $scrim['scr_id']; ?>">
                                                    <button type="submit" class="text-current hover:text-error-text p-1">
                                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- version telefono -->
                <div class="md:hidden space-y-4">
                    <?php
                    $any_scrim = false;
                    for ($day = 1; $day <= $numberDays; $day++):
                        $dayScrims = array_filter($scrims, function ($s) use ($day, $month, $year) {
                            $d = strtotime($s['scr_fecha_juego']);
                            return date('j', $d) == $day && date('n', $d) == $month && date('Y', $d) == $year;
                        });

                        if (empty($dayScrims)) continue;
                        $any_scrim = true;

                        $isToday = ($day == date('j') && $month == date('n') && $year == date('Y'));
                        $timestamp = mktime(0, 0, 0, $month, $day, $year);
                        $dia_nombre = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'][date('w', $timestamp)];
                    ?>
                        <div class="bg-surface border-2 border-primary p-4 shadow-hard-sm">
                            <div class="flex items-center gap-3 mb-3 border-2 border-subtle pb-2">
                                <span class="text-2xl font-black text-primary"><?php echo $day; ?></span>
                                <div class="flex flex-col leading-none">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-muted"><?php echo $dia_nombre; ?></span>
                                    <?php if ($isToday): ?>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-scrimsync">Hoy</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <?php foreach ($dayScrims as $scrim): ?>
                                    <?php
                                    $statusClass = 'bg-subtle text-secondary';
                                    $dotClass = 'bg-muted';
                                    $desc = strtolower($scrim['est_descripcion']);
                                    $is_cancelled = (str_contains($desc, 'cancel'));
                                    $is_accepted = (str_contains($desc, 'confirm') || str_contains($desc, 'aceptad'));

                                    if ($is_accepted) {
                                        $statusClass = 'bg-success-light text-success-text border border-emerald-100';
                                        $dotClass = 'bg-emerald-500';
                                    } elseif (str_contains($desc, 'pendient')) {
                                        $statusClass = 'bg-amber-50 text-amber-700 border border-amber-100';
                                        $dotClass = 'bg-amber-500';
                                    } elseif ($is_cancelled) {
                                        $statusClass = 'bg-error-light text-error-text border border-rose-100 line-through';
                                        $dotClass = 'bg-rose-500';
                                    }
                                    ?>
                                    <div class="flex items-center justify-between gap-3 p-3 rounded-none font-bold transition-colors <?php echo $statusClass; ?>"
                                        <?php if ($is_accepted): ?>
                                        onclick="document.getElementById('scrim_modal_<?php echo $scrim['scr_id']; ?>').showModal()"
                                        <?php endif; ?>>

                                        <div class="flex items-center gap-3 flex-1">
                                            <span class="text-xs font-black"><?php echo substr($scrim['scr_hora_inicio'], 0, 5); ?></span>
                                            <div class="w-2 h-2 <?php echo $dotClass; ?>"></div>
                                            <span class="uppercase tracking-tight text-sm"><?php echo htmlspecialchars($scrim['opponent_name']); ?></span>
                                        </div>

                                        <?php if ($is_accepted && $is_captain): ?>
                                            <form action="../../scrim/controller_scrim.php" method="POST"
                                                onsubmit="event.stopPropagation(); return confirm('¿ESTÁS SEGURO?');"
                                                class="z-10">
                                                <input type="hidden" name="action" value="cancelar">
                                                <input type="hidden" name="scr_id" value="<?php echo $scrim['scr_id']; ?>">
                                                <button type="submit" class="text-current hover:text-error-text p-1">
                                                    <i data-lucide="x" class="w-5 h-5"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endfor; ?>

                    <?php if (!$any_scrim): ?>
                        <div class="bg-surface border-2 border-dashed border-muted p-8 text-center">
                            <i data-lucide="calendar-off" class="w-10 h-10 text-muted mx-auto mb-2"></i>
                            <p class="text-xs font-bold uppercase tracking-widest text-muted">No hay scrims para este mes</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- modal flotante -->
                <?php foreach ($scrims as $scrim):
                ?>
                    <dialog id="scrim_modal_<?php echo $scrim['scr_id']; ?>"
                        class="m-auto rounded-none border-2 border-primary p-0 shadow-hard">
                        <div class="bg-surface w-80 overflow-hidden text-left">
                            <div class="flex justify-between items-center p-4 border-b-2 border-subtle">
                                <h3 class="text-lg font-black uppercase text-primary tracking-tight truncate">
                                    <?php echo htmlspecialchars($scrim['opponent_name']); ?>
                                </h3>
                                <form method="dialog">
                                    <button class="text-secondary hover:text-primary transition-colors">
                                        <i data-lucide="x" class="w-5 h-5"></i>
                                    </button>
                                </form>
                            </div>

                            <div class="p-4 space-y-3">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-secondary px-1">Fecha y Hora</label>
                                    <p class="text-xl font-black text-primary">
                                        <?php echo date('d/m', strtotime($scrim['scr_fecha_juego'])) . ' @ ' . substr($scrim['scr_hora_inicio'], 0, 5); ?>
                                    </p>
                                </div>

                                <?php if ($is_captain): ?>
                                    <div class="pt-3 border-t-2 border-subtle">
                                        <form action="../../scrim/controller_scrim.php" method="POST" onsubmit="return confirm('¿ESTÁS SEGURO?');">
                                            <input type="hidden" name="action" value="cancelar">
                                            <input type="hidden" name="scr_id" value="<?php echo $scrim['scr_id']; ?>">
                                            <button type="submit"
                                                class="w-full transition-all text-xs text-secondary font-black uppercase tracking-widest px-6 py-3 hover:bg-error-text hover:text-surface flex justify-center">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                                Cancelar Scrim
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </dialog>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>

<?php include '../../../includes/footer.php'; ?>
