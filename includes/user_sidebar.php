<?php
$current_uri = $_SERVER['REQUEST_URI'];

// Check for unread notifications
$unread_notifications = false;
if (isset($_SESSION['usu_id']) && isset($conn)) {
    $sid_usu_id = $_SESSION['usu_id'];
    $sid_sql = "SELECT COUNT(*) as c FROM notificacion WHERE usu_id = '$sid_usu_id' AND not_estado_leido = 0";
    $sid_res = mysqli_query($conn, $sid_sql);
    if ($sid_res) {
        $row = mysqli_fetch_assoc($sid_res);
        if ($row['c'] > 0) {
            $unread_notifications = true;
        }
    }
}
?>

<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden transition-opacity opacity-0"></div>

<aside id="sidebar" class="w-64 bg-surface border-r-2 border-primary flex flex-col h-screen fixed left-0 top-0 pt-20 z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-300">
  <div class="px-6">
    <nav class="space-y-2">
      <a href="#"
        class="cursor-not-allowed flex items-center gap-3 px-4 py-3 text-sm font-bold transition-colors border-l-4 <?php echo (strpos($current_uri, 'index.php') !== false && strpos($current_uri, 'modules') === false) ? 'text-primary bg-subtle border-primary' : 'text-secondary border-transparent hover:text-primary hover:bg-subtle hover:border-primary'; ?>">
        <i data-lucide="home" class="w-4 h-4"></i>
        Dashboard
      </a>
    </nav>

    <nav class="space-y-2">
      <a href="/modules/team/profile/index.php"
        class="flex items-center gap-3 px-4 py-3 text-sm font-bold transition-colors border-l-4 <?php echo (strpos($current_uri, 'modules/team/profile/') !== false) ? 'text-primary bg-subtle border-primary' : 'text-secondary border-transparent hover:text-primary hover:bg-subtle hover:border-primary'; ?>">
        <i data-lucide="shield" class="w-4 h-4"></i>
        Mi Equipo
      </a>
    </nav>

    <nav class="space-y-2">
      <a href="#"
        class="cursor-not-allowed flex items-center gap-3 px-4 py-3 text-sm font-bold text-secondary hover:text-primary hover:bg-subtle transition-colors border-l-4 border-transparent hover:border-primary">
        <i data-lucide="search" class="w-4 h-4"></i>
        Buscar Scrim
      </a>
    </nav>

    <nav class="space-y-2">
      <a href="/modules/user/calendar/index.php"
        class="flex items-center gap-3 px-4 py-3 text-sm font-bold transition-colors border-l-4 <?php echo (strpos($current_uri, 'modules/user/calendar/') !== false) ? 'text-primary bg-subtle border-primary' : 'text-secondary border-transparent hover:text-primary hover:bg-subtle hover:border-primary'; ?>">
        <i data-lucide="calendar" class="w-4 h-4"></i>
        Calendario
      </a>
    </nav>

    <nav class="space-y-2">
      <a href="/modules/user/notification/index.php"
        class="flex items-center gap-3 px-4 py-3 text-sm font-bold transition-colors border-l-4 <?php echo (strpos($current_uri, 'modules/user/notification/') !== false) ? 'text-primary bg-subtle border-primary' : 'text-secondary border-transparent hover:text-primary hover:bg-subtle hover:border-primary'; ?>">
        <div class="relative">
          <i data-lucide="bell" class="w-4 h-4"></i>
          <?php if ($unread_notifications): ?>
            <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
            </span>
          <?php endif; ?>
        </div>
        Notificaciones
      </a>
    </nav>

    <nav class="space-y-2">
      <a href="/modules/user/profile/index.php"
        class="flex items-center gap-3 px-4 py-3 text-sm font-bold transition-colors border-l-4 <?php echo (strpos($current_uri, 'modules/user/profile/') !== false) ? 'text-primary bg-subtle border-primary' : 'text-secondary border-transparent hover:text-primary hover:bg-subtle hover:border-primary'; ?>">
        <i data-lucide="user" class="w-4 h-4"></i>
        Mi Perfil
      </a>
    </nav>
  </div>
</aside>