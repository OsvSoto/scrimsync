<nav class="fixed top-0 left-0 w-full h-16 bg-surface border-b-2 border-primary z-50 flex items-center justify-between px-6">
    <div class="flex items-center gap-2">
        <h1 class="text-2xl font-black text-primary tracking-tighter">SCRIMSYNC</h1>
        <span class="bg-primary text-white text-[10px] px-2 py-0.5 font-bold uppercase tracking-widest">Admin</span>
    </div>
    <div class="flex items-center gap-4">
        <span class="text-sm font-bold text-secondary">
            Hola, <?php echo isset($_SESSION['alias']) ? htmlspecialchars($_SESSION['alias']) : 'Admin'; ?>
        </span>
    </div>
</nav>