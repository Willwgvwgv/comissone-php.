<!-- Mobile Bottom Navbar -->
<?php if (isset($_SESSION['user_id'])): ?>
    <nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-gray-200 flex justify-around py-3 z-50">
        <a href="dashboard.php" class="flex flex-col items-center text-gray-400 hover:text-indigo-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-xs mt-1">Home</span>
        </a>
        <a href="imoveis.php" class="flex flex-col items-center text-gray-400 hover:text-indigo-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <span class="text-xs mt-1">Imóveis</span>
        </a>
        <a href="receitas.php" class="flex flex-col items-center text-green-500 hover:text-green-700">
            <svg class="w-8 h-8 -mt-4 bg-green-100 rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <!-- Center Button Style -->
        </a>
        <a href="despesas.php" class="flex flex-col items-center text-gray-400 hover:text-indigo-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-xs mt-1">Despesas</span>
        </a>
        <a href="conciliacao.php" class="flex flex-col items-center text-gray-400 hover:text-indigo-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="text-xs mt-1">OFX</span>
        </a>
    </nav>
<?php endif; ?>

<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('service-worker.js')
        .then(req => console.log('SW registrado!'))
        .catch(err => console.log('SW erro', err));
    }
</script>
</body>
</html>
