<?php
// dashboard.php
require_once 'src/auth.php';
checkAuth();
require_once 'assets/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Visão Geral</h1>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Receitas</dt>
                            <dd class="text-lg font-medium text-gray-900" id="kpi-receitas">Carregando...</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Despesas</dt>
                            <dd class="text-lg font-medium text-gray-900" id="kpi-despesas">Carregando...</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Balanço</dt>
                            <dd class="text-lg font-medium text-gray-900" id="kpi-balanco">Carregando...</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    
    <!-- Second Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
         <!-- Comissões Pendentes -->
         <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <!-- Icone Moeda -->
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Comissões Pendentes</dt>
                            <dd class="text-lg font-medium text-gray-900" id="kpi-comissoes-pend">Carregando...</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
         <!-- Comissões Atrasadas -->
         <div class="bg-white overflow-hidden shadow rounded-lg p-5">
             <h3 class="text-sm font-medium text-gray-500 truncate mb-2">Comissões em Atraso</h3>
             <p class="text-2xl font-bold text-red-600" id="kpi-comissoes-atrasadas">0</p>
             <p class="text-xs text-gray-400">Vencidas e não pagas</p>
         </div>
         
         <!-- Top Corretores -->
         <div class="bg-white overflow-hidden shadow rounded-lg p-5 col-span-1 md:col-span-2">
             <h3 class="text-sm font-medium text-gray-500 truncate mb-2">Top Corretores (Recebimentos)</h3>
             <ul id="list-top-corretores" class="text-sm space-y-2"></ul>
         </div>
    </div>

    <!-- Chart -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Fluxo de Caixa (Últimos meses)</h3>
        <div class="h-64">
            <canvas id="financeChart"></canvas>
        </div>
    </div>

    <!-- Badges -->
    <div class="text-center mb-8" id="status-badge"></div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    async function loadDashboardFunc() {
        // Mock data logic for simplicity in this MVP, 
        // ideally fetch from a dedicated dashboard API endpoint logic
        // But we can reuse the listing logic or create a specific endpoint. 
        // For now, let's fetch list logic and calculate in JS (simplest for "no-framework" frontend)
        
        const [resRec, resDesp] = await Promise.all([
            fetch('api/crud_receita.php').then(r => r.json()),
            fetch('api/crud_despesa.php').then(r => r.json())
        ]);

        let totalRec = 0;
        let totalDesp = 0;
        
        // Sum totals
        resRec.forEach(r => totalRec += parseFloat(r.valor));
        resDesp.forEach(d => totalDesp += parseFloat(d.valor));
        
        const balanco = totalRec - totalDesp;
        
        document.getElementById('kpi-receitas').innerText = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(totalRec);
        document.getElementById('kpi-despesas').innerText = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(totalDesp);
        const elBalanco = document.getElementById('kpi-balanco');
        elBalanco.innerText = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(balanco);
        document.getElementById('kpi-balanco').innerText = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(balanco);
        
        // Load Commission Stats
        const resStats = await fetch('api/dashboard_stats.php').then(r => r.json());
        
        document.getElementById('kpi-comissoes-pend').innerText = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(resStats.comissoes_pendentes);
        document.getElementById('kpi-comissoes-atrasadas').innerText = resStats.comissoes_atrasadas.qtd + " (R$ " + resStats.comissoes_atrasadas.valor + ")";
        
        const listTop = document.getElementById('list-top-corretores');
        listTop.innerHTML = '';
        if(resStats.top_corretores.length === 0) listTop.innerHTML = '<li class="text-gray-400">Nenhum dado</li>';
        resStats.top_corretores.forEach((c, idx) => {
             listTop.innerHTML += `<li class="flex justify-between"><span>${idx+1}. ${c.nome}</span> <span class="font-bold">R$ ${parseFloat(c.total_recebido).toFixed(2)}</span></li>`;
        });

        // Badge
        const badge = document.getElementById('status-badge');
        if (balanco >= 0) {
            badge.innerHTML = '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800"> Empresa Positiva 🚀</span>';
        } else {
            badge.innerHTML = '<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800"> Empresa no Vermelho ⚠️</span>';
        }

        // Chart Data (Mocking monthly distribution for demo if dates are sparse)
        // In real app, group by month.
        const ctx = document.getElementById('financeChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [
                    {
                        label: 'Receitas',
                        data: [totalRec * 0.1, totalRec * 0.2, totalRec * 0.15, totalRec * 0.25, totalRec * 0.2, totalRec * 0.1], // Fake distribution
                        backgroundColor: 'rgba(16, 185, 129, 0.5)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Despesas',
                        data: [totalDesp * 0.1, totalDesp * 0.2, totalDesp * 0.15, totalDesp * 0.25, totalDesp * 0.2, totalDesp * 0.1], // Fake distribution
                        backgroundColor: 'rgba(239, 68, 68, 0.5)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
    loadDashboardFunc();
</script>

<?php require_once 'assets/footer.php'; ?>
