<script>
      lucide.createIcons();

      // Mobile Sidebar Toggle
      document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        if (btn && sidebar && overlay) {
          btn.addEventListener('click', () => {
            const isClosed = sidebar.classList.contains('-translate-x-full');
            if (isClosed) {
              // Open
              sidebar.classList.remove('-translate-x-full');
              overlay.classList.remove('hidden');
              // Small delay to allow display:block to apply before opacity transition
              setTimeout(() => {
                  overlay.classList.remove('opacity-0');
              }, 10);
            } else {
              // Close
              sidebar.classList.add('-translate-x-full');
              overlay.classList.add('opacity-0');
              setTimeout(() => {
                  overlay.classList.add('hidden');
              }, 300);
            }
          });

          overlay.addEventListener('click', () => {
             sidebar.classList.add('-translate-x-full');
             overlay.classList.add('opacity-0');
             setTimeout(() => {
                 overlay.classList.add('hidden');
             }, 300);
          });
        }
      });
    </script>
</body>
</html>