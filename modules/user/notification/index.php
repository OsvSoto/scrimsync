<?php
// modules/user/notification/index.php
session_start();
require_once '../../../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../../../modules/auth/login.php");
    exit;
}
$usu_id = $_SESSION['usu_id'];

$sql = "SELECT
          not_id,
          equ_id,
          scr_id,
          not_tipo,
          not_asunto,
          not_mensaje,
          not_estado_leido,
          not_fecha
        FROM notificacion WHERE usu_id = ?
        ORDER BY not_fecha DESC;";
$result = $conn->execute_query($sql, [$usu_id]);
$notificaciones = [];
$hasUnread = false;

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $notificaciones[] = $row;
        if ($row['not_estado_leido'] == 0) {
            $hasUnread = true;
        }
    }
}

include '../../../includes/header.php';
include '../../../includes/user_navbar.php';
?>

<div class="flex min-h-screen bg-background">
    <main class="flex-1 w-full pt-16 pb-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if (isset($_SESSION['flash_msg'])): ?>
                <div class="mb-6 bg-success-light border-2 border-success-border p-4 shadow-hard-success flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <i data-lucide="check-circle" class="text-success-text w-5 h-5 shrink-0 mt-0.5"></i>
                        <p class="text-success-text font-black uppercase text-xs tracking-widest leading-relaxed">
                            <?php echo htmlspecialchars($_SESSION['flash_msg']); ?>
                        </p>
                    </div>
                    <button onclick="this.parentElement.remove();" class="text-success-text hover:opacity-70 shrink-0">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <?php unset($_SESSION['flash_msg']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="mb-6 bg-error-light border-2 border-error-border p-4 shadow-hard-error flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-circle" class="text-error-text w-5 h-5 shrink-0 mt-0.5"></i>
                        <p class="text-error-text font-black uppercase text-xs tracking-widest leading-relaxed">
                            <?php echo htmlspecialchars($_SESSION['flash_error']); ?>
                        </p>
                    </div>
                    <button onclick="this.parentElement.remove();" class="text-error-text hover:opacity-70 shrink-0">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-primary uppercase tracking-tight mb-2">
                        Notificaciones
                    </h2>
                    <p class="text-secondary text-sm font-medium uppercase tracking-widest">
                        Tienes <?php echo count($notificaciones); ?> alertas en total
                    </p>
                </div>

                <div class="flex flex-wrap gap-2 w-full sm:w-auto sm:justify-end">
                    <?php if ($hasUnread): ?>
                        <form action="controller_notification.php" method="POST">
                            <input type="hidden" name="action" value="mark_all_read">
                            <button type="submit"
                                class="bg-primary transition-all hover:bg-primary-hover text-xs text-surface font-black uppercase tracking-widest px-3 py-3 flex items-center gap-1 cursor-pointer">
                                <i data-lucide="check-check" class="w-4 h-4"></i>
                                Marcar leídos
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if (!empty($notificaciones)): ?>
                        <form action="controller_notification.php" method="POST">
                            <input type="hidden" name="action" value="clear_notifications">
                            <button type="submit"
                                class="transition-all text-xs text-secondary font-black uppercase tracking-widest px-3 py-3 hover:bg-error-text hover:text-surface flex items-center cursor-pointer"
                                title="Eliminar Todo">
                                <i data-lucide="trash" class="w-4 h-4"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="space-y-4">
                <?php if (empty($notificaciones)): ?>
                    <div class="text-center py-20 border-2 border-dashed border-border text-muted bg-surface shadow-sm">
                        <i data-lucide="bell" class="mx-auto mb-4 opacity-10 w-12 h-12"></i>
                        <p class="font-bold uppercase tracking-widest text-xs">No hay alertas nuevas</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notificaciones as $notificacion): ?>
                        <div class="p-4 sm:p-6 border border-border shadow-sm relative transition-all <?php echo $notificacion['not_estado_leido'] ? 'bg-subtle' : 'bg-surface ring-1 ring-inset ring-border'; ?>">
                            <?php if (!$notificacion['not_estado_leido']): ?>
                                <div class="absolute top-4 right-4 w-2 h-2 bg-blue-500 rounded-full"></div>
                            <?php endif; ?>

                            <div class="flex flex-col sm:flex-row gap-4">

                                <div class="mt-1 shrink-0 hidden sm:block">
                                    <?php if ($notificacion['not_tipo'] === 'SCRIM'): ?>
                                        <i data-lucide="shield-alert" class="text-amber-500 w-6 h-6"></i>
                                    <?php elseif ($notificacion['not_tipo'] === 'INVITACION'): ?>
                                        <i data-lucide="bell" class="text-emerald-500 w-6 h-6"></i>
                                    <?php else: ?>
                                        <i data-lucide="info" class="text-muted w-6 h-6"></i>
                                    <?php endif; ?>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 sm:hidden">
                                        <?php if ($notificacion['not_tipo'] === 'SCRIM'): ?>
                                            <i data-lucide="shield-alert" class="text-amber-500 w-4 h-4"></i>
                                        <?php elseif ($notificacion['not_tipo'] === 'INVITACION'): ?>
                                            <i data-lucide="bell" class="text-emerald-500 w-4 h-4"></i>
                                        <?php else: ?>
                                            <i data-lucide="info" class="text-muted w-4 h-4"></i>
                                        <?php endif; ?>
                                        <span class="text-[10px] font-black text-secondary uppercase tracking-widest">
                                            <?php echo date('d/m/Y', strtotime($notificacion['not_fecha'])); ?>
                                        </span>
                                    </div>

                                    <h3 class="font-black text-base sm:text-lg text-primary uppercase tracking-tight mb-1 truncate">
                                        <?php echo htmlspecialchars($notificacion['not_asunto']); ?>
                                    </h3>

                                    <p class="text-secondary text-sm font-medium mb-3 leading-relaxed">
                                        <?php echo htmlspecialchars($notificacion['not_mensaje']); ?>
                                    </p>

                                    <?php if ($notificacion['not_tipo'] === 'INVITACION' || $notificacion['not_tipo'] === 'SCRIM'): ?>
                                        <div class="flex items-center gap-3 mb-4">
                                            <?php if ($notificacion['not_tipo'] === 'INVITACION'): ?>
                                                <form action="../../../modules/team/actions/invite_member.php" method="POST">
                                                    <input type="hidden" name="action" value="accept_invite">
                                                    <input type="hidden" name="not_id" value="<?php echo $notificacion['not_id']; ?>">
                                                    <input type="hidden" name="equ_id" value="<?php echo $notificacion['equ_id']; ?>">
                                                    <button type="submit"
                                                        class="bg-black text-surface px-4 py-2 text-[10px] font-black uppercase tracking-widest flex items-center gap-2 hover:bg-success-border cursor-pointer">
                                                        <i data-lucide="check" class="w-3 h-3"></i>
                                                        Aceptar
                                                    </button>
                                                </form>
                                            <?php elseif ($notificacion['not_tipo'] === 'SCRIM'): ?>
                                                <form action="../../scrim/controller_scrim.php" method="POST">
                                                    <input type="hidden" name="action" value="accept_scrim">
                                                    <input type="hidden" name="not_id" value="<?php echo $notificacion['not_id']; ?>">
                                                    <input type="hidden" name="scr_id" value="<?php echo $notificacion['scr_id']; ?>">
                                                    <button type="submit"
                                                        class="bg-black  text-surface px-4 py-2 text-[10px] font-black uppercase tracking-widest flex items-center gap-2 hover:bg-success-border cursor-pointer transition-all ">
                                                        <i data-lucide="check" class="w-3 h-3"></i>
                                                        Aceptar
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <form action="<?php echo ($notificacion['not_tipo'] === 'SCRIM') ? '../../scrim/controller_scrim.php' : 'controller_notification.php'; ?>" method="POST">
                                                <input type="hidden" name="action" value="<?php echo ($notificacion['not_tipo'] === 'SCRIM') ? 'reject_scrim' : 'reject_invite'; ?>">
                                                <input type="hidden" name="not_id" value="<?php echo $notificacion['not_id']; ?>">
                                                <?php if ($notificacion['not_tipo'] === 'SCRIM'): ?>
                                                    <input type="hidden" name="scr_id" value="<?php echo $notificacion['scr_id']; ?>">
                                                <?php endif; ?>
                                                <button type="submit"
                                                    class="bg-subtle text-primary px-4 py-2 text-[10px] font-black uppercase tracking-widest flex items-center gap-2 hover:bg-error-text hover:text-surface cursor-pointer transition-all ">
                                                    <i data-lucide="x" class="w-3 h-3"></i>
                                                    Rechazar
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>

                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-[10px] font-black px-2 py-0.5 border border-border text-secondary uppercase tracking-widest">
                                            <?php echo htmlspecialchars($notificacion['not_tipo']); ?>
                                        </span>
                                        <?php if (!$notificacion['not_estado_leido']): ?>
                                            <form action="controller_notification.php" method="POST">
                                                <input type="hidden" name="action" value="mark_as_read">
                                                <input type="hidden" name="not_id" value="<?php echo $notificacion['not_id']; ?>">
                                                <button type="submit"
                                                    class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline cursor-pointer">
                                                    Marcar leído
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="flex flex-row sm:flex-col items-start sm:items-end justify-between sm:justify-start gap-3 flex-shrink-0 sm:ml-2 mt-2 sm:mt-0 border-t sm:border-0 border-border pt-3 sm:pt-0">
                                    <span class="hidden sm:block text-[10px] font-black text-secondary uppercase tracking-widest whitespace-nowrap">
                                        <?php echo date('d/m/Y', strtotime($notificacion['not_fecha'])); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include '../../../includes/footer.php'; ?>
