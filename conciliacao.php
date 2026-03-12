<?php
// conciliacao.php
require_once 'src/auth.php';
checkAuth();
require_once 'assets/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Conciliação Bancária (OFX)</h1>

    <!-- Upload -->
    <div class="bg-white shadow sm:rounded-lg p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Importar Arquivo</h3>
        <form id="formUpload" class="flex items-center space-x-4">
            <input type="file" name="ofx_file" accept=".ofx" required class="block w-full text-sm text-gray-500
              file:mr-4 file:py-2 file:px-4
              file:rounded-full file:border-0
              file:text-sm file:font-semibold
              file:bg-indigo-50 file:text-indigo-700
              hover:file:bg-indigo-100">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">Importar</button>
        </form>
        <div id="uploadMsg" class="mt-2 text-sm"></div>
    </div>

    <!-- Lista de Pendentes -->
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">Transações Pendentes</h3>
        <button onclick="clearPendentes()" class="text-sm text-red-600 hover:text-red-800 font-semibold flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 0 00-1 1v3M4 7h16" />
            </svg>
            Limpar Pendentes
        </button>
    </div>
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul id="list" class="divide-y divide-gray-200">
            <!-- JS -->
        </ul>
    </div>
</div>

<!-- Modal Conciliação -->
<div id="modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Conciliar Transação</h3>
                <p class="text-sm text-gray-500 mb-4" id="modal-desc"></p>
                <input type="hidden" id="ofx_id">
                <input type="hidden" id="ofx_valor">
                <input type="hidden" id="ofx_data">
                <input type="hidden" id="ofx_tipo">

                <!-- Tabs -->
                <div class="mb-4 border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button onclick="switchTab('link')" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm border-indigo-500 text-indigo-600" id="tab-link">Vincular Existente</button>
                        <button onclick="switchTab('new')" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" id="tab-new">Criar Novo</button>
                    </nav>
                </div>

                <!-- Tab: Link -->
                <div id="content-link">
                    <p class="text-sm text-gray-600 mb-2">Selecione um lançamento existente:</p>
                    <select id="select-lancamento" class="block w-full border border-gray-300 rounded-md py-2 px-3 mb-2">
                        <option value="">Carregando sugestões...</option>
                    </select>
                    <button onclick="confirmLink()" class="w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Vincular</button>
                </div>

                <!-- Tab: New -->
                <div id="content-new" class="hidden">
                    <p class="text-sm text-gray-600 mb-2">Preencha os dados para criar:</p>
                    <div class="space-y-3">
                        <input type="text" id="new_desc" class="block w-full border border-gray-300 rounded py-2 px-3" placeholder="Descrição">
                        <!-- Categoria hardcoded simplificada. Ideal seria carregar dinamicamente baseado no tipo -->
                        <select id="new_cat" class="block w-full border border-gray-300 rounded py-2 px-3">
                             <option value="Outros">Outros</option>
                             <option value="Manutenção">Manutenção</option>
                             <option value="Aluguel">Aluguel</option>
                             <option value="Contas Fixas">Contas Fixas</option>
                        </select>
                         <select id="new_imovel" class="block w-full border border-gray-300 rounded py-2 px-3">
                            <option value="">Imóvel (Opcional)</option>
                        </select>
                        <button onclick="confirmNew()" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Criar e Vincular</button>
                    </div>
                </div>

            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
    async function getPendentes() {
        // Mock ou endpoint real se tivesse filtro. 
        // Como o import_ofx apenas insere, precisamos de um endpoint pra listar OFX (não fiz crud_ofx, vou improvisar uma query no JS ou assumir endpoint)
        // **Faltou criar um endpoint GET para listar OFX pendentes.**
        // Vou usar um pequeno hack: criar um arquivo `api/list_ofx.php` agora ou embutir no import?
        // Melhor, vou criar `api/list_ofx.php`.
        const res = await fetch('api/list_ofx.php'); 
        if (!res.ok) return [];
        return await res.json();
    }

    async function loadList() {
        const list = document.getElementById('list');
        list.innerHTML = '<li class="p-4 text-center">Carregando...</li>';
        
        try {
            const data = await getPendentes();
            list.innerHTML = '';
            
            if (data.length === 0) {
                list.innerHTML = '<li class="p-4 text-center text-gray-500">Nenhuma transação pendente.</li>';
                return;
            }

            data.forEach(item => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <div class="block hover:bg-gray-50 cursor-pointer" onclick='openModal(${JSON.stringify(item)})'>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-indigo-600 truncate">${item.descricao_original}</p>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${item.tipo === 'CREDIT' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        R$ ${parseFloat(item.valor).toFixed(2)}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        Data: ${item.data.split('-').reverse().join('/')}
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                   Status: ${item.status}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                list.appendChild(li);
            });
        } catch (e) {
            console.error(e);
            list.innerHTML = '<li class="p-4 text-center text-red-500">Erro ao carregar (Faltando endpoint?)</li>';
        }
    }

    // Upload
    document.getElementById('formUpload').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const msg = document.getElementById('uploadMsg');
        msg.innerText = 'Enviando...';
        
        const res = await fetch('api/import_ofx.php', { method: 'POST', body: formData });
        const json = await res.json();
        
        msg.innerText = json.message || json.error;
        msg.className = res.ok ? 'mt-2 text-sm text-green-600' : 'mt-2 text-sm text-red-600';
        
        if (res.ok) loadList();
    });

    // Modal & Conciliation Logic
    const modal = document.getElementById('modal');
    
    async function openModal(item) {
        document.getElementById('ofx_id').value = item.id;
        document.getElementById('ofx_valor').value = item.valor;
        document.getElementById('ofx_data').value = item.data;
        document.getElementById('ofx_tipo').value = item.tipo; // DEBIT/CREDIT
        
        document.getElementById('modal-desc').innerText = `${item.descricao_original} - R$ ${item.valor}`;
        document.getElementById('new_desc').value = item.descricao_original;
        
        modal.classList.remove('hidden');

        // Load suggestions (Improvised logic: fetch recent items and client filters)
        // Ideally backend should return suggestions.
        // We will fetch ALL receitas/despesas depending on type
        const type = item.tipo === 'CREDIT' ? 'receita' : 'despesa';
        const endpoint = type === 'receita' ? 'api/crud_receita.php' : 'api/crud_despesa.php';
        
        const res = await fetch(endpoint);
        const records = await res.json();
        const select = document.getElementById('select-lancamento');
        select.innerHTML = '<option value="">Selecione...</option>';
        
        records.forEach(r => {
            select.innerHTML += `<option value="${r.id}">${r.data} - ${r.descricao} (R$ ${r.valor})</option>`;
        });

        // Load Imoveis for new
        const resIm = await fetch('api/crud_imovel.php');
        const imData = await resIm.json();
        const selIm = document.getElementById('new_imovel');
        selIm.innerHTML = '<option value="">Imóvel (Opcional)</option>';
        imData.imoveis.forEach(i => selIm.innerHTML += `<option value="${i.id}">${i.endereco_completo}</option>`);
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    function switchTab(tab) {
        document.querySelectorAll('[id^="content-"]').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('[id^="tab-"]').forEach(el => {
            el.className = "whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300";
        });

        document.getElementById('content-' + tab).classList.remove('hidden');
        document.getElementById('tab-' + tab).className = "whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm border-indigo-500 text-indigo-600";
    }

    async function confirmLink() {
        const ofx_id = document.getElementById('ofx_id').value;
        const lancamento_id = document.getElementById('select-lancamento').value;
        const tipo = document.getElementById('ofx_tipo').value === 'CREDIT' ? 'receita' : 'despesa';
        
        if(!lancamento_id) return alert('Selecione um lançamento');

        await fetch('api/reconcile_transaction.php', {
            method: 'POST',
            body: JSON.stringify({ action: 'link', ofx_id, lancamento_id, tipo_lancamento: tipo })
        });
        closeModal();
        loadList();
    }

    async function confirmNew() {
        const ofx_id = document.getElementById('ofx_id').value;
        const tipo = document.getElementById('ofx_tipo').value === 'CREDIT' ? 'receita' : 'despesa';
        
        const dados_novo = {
            descricao: document.getElementById('new_desc').value,
            categoria: document.getElementById('new_cat').value,
            imovel_id: document.getElementById('new_imovel').value,
            valor: document.getElementById('ofx_valor').value,
            data: document.getElementById('ofx_data').value
        };

        await fetch('api/reconcile_transaction.php', {
            method: 'POST',
            body: JSON.stringify({ action: 'create', ofx_id, tipo_lancamento: tipo, dados_novo })
        });
        closeModal();
        loadList();
    }

    loadList();
</script>

<?php require_once 'assets/footer.php'; ?>
