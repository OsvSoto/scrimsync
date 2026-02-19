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

<nav class="fixed top-0 left-0 w-full h-10 bg-surface border-b-2 border-primary z-50">
  <div class="max-w-6xl mx-auto flex items-center justify-between px-2 sm:px-4 lg:px-8">
    <!-- ScrimSync - Buscar Scrim - Calendario - Mi Equipo -->
    <div class="flex items-center gap-3">
      <a href="/index.php" class="flex items-center gap-2">
        <span class="text-2xl font-black tracking-tighter text-primary transition-colors hover:text-scrimsync">SCRIMSYNC</span>
      </a>
      <a href="/modules/scrim/index.php"
        class="hover:underline flex items-center gap-1 px-2 py-2 text-sm transition-colors <?php echo (strpos($current_uri, 'modules/scrim/') !== false) ? 'text-primary font-bold border-primary' : 'text-secondary border-transparent hover:text-primary hover:border-primary'; ?>">
        <i data-lucide="search" class="w-4 h-4 "></i>
        <span class="hidden md:inline ">Buscar Scrim</span>
      </a>

      <a href="/modules/user/calendar/index.php"
        class="hover:underline flex items-center gap-1 px-2 py-2 text-sm transition-colors <?php echo (strpos($current_uri, 'modules/user/calendar/') !== false) ? 'text-primary font-bold border-primary' : 'text-secondary border-transparent hover:text-primary hover:border-primary'; ?>">
        <i data-lucide="calendar" class="w-4 h-4"></i>
        <span class="hidden md:inline">Calendario</span>
      </a>

      <a href="/modules/team/profile/index.php"
        class="hover:underline flex items-center gap-1 px-2 py-2 text-sm transition-colors <?php echo (strpos($current_uri, 'modules/team/profile/') !== false) ? 'text-primary font-bold border-primary' : 'text-secondary border-transparent hover:text-primary hover:border-primary'; ?>">
        <i data-lucide="shield" class="w-4 h-4"></i>
        <span class="hidden md:inline">Mi Equipo</span>
      </a>
    </div>

    <!-- Notificaciones - Mi Perfil/Logout -->
    <div class="flex items-center gap-1">
      <a href="/modules/user/notification/index.php"
        class="flex items-center gap-1 px-2 py-2 text-sm transition-colors <?php echo (strpos($current_uri, 'modules/user/notification/') !== false) ? 'text-primary font-bold border-primary' : 'text-secondary border-transparent hover:text-primary hover:border-primary'; ?>">
        <div class="relative">
          <i data-lucide="bell" class="w-4 h-4"></i>
          <?php if ($unread_notifications): ?>
            <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
            </span>
          <?php endif; ?>
        </div>
      </a>

      <div class="dropdown dropdown-end">
        <div tabindex="0" role="button"
             class="bg-surface text-secondary hover:text-primary shadow-none rounded-none" title="Perfil">
          <i data-lucide="user" class="w-4 h-4"></i>
        </div>
        <ul tabindex="0"
            class="dropdown-content z-[1] menu p-2 shadow-hard bg-surface border-2 border-primary w-max min-w-[160px] rounded-none mt-1">
          <li>
            <a href="/modules/user/profile/index.php"
              class="flex items-center gap-1 px-2 py-2 text-sm transition-colors <?php echo (strpos($current_uri, 'modules/user/profile/') !== false) ? 'text-primary font-bold border-primary' : 'text-secondary border-transparent hover:text-primary hover:border-primary'; ?>">
              <i data-lucide="user" class="w-4 h-4"></i>
              <span>Mi Perfil</span>
            </a>
            <a href="/modules/auth/logout.php"
              class="flex items-center gap-1 px-2 py-2 text-sm transition-colors text-secondary hover:text-error-text">
              <i data-lucide="log-out" class="w-4 h-4"></i>
              <span>Salir</span>
            </a>
        </ul>
      </div>

    </div>
  </div>
</nav>
