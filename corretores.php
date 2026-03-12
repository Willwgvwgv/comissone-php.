<?php
// corretores.php
require_once 'src/auth.php';
checkAuth();
require_once 'assets/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Corretores</h1>
        <button onclick="openModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
            + Novo Corretor
        </button>
    </div>

    <!-- Lista -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul id="list" class="divide-y divide-gray-200"></ul>
    </div>
</div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeModal()"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form id="formCorretor">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Novo Corretor</h3>
                    <input type="hidden" name="id" id="id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Dados Pessoais -->
                        <div class="col-span-2"><h4 class="text-sm font-bold text-gray-500 uppercase">Dados Pessoais</h4></div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome</label>
                            <input type="text" name="nome" id="nome" required class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CPF/CNPJ</label>
                            <input type="text" name="cpf_cnpj" id="cpf_cnpj" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CRECI</label>
                            <input type="text" name="creci" id="creci" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>
                        
                        <!-- Dados Bancários -->
                        <div class="col-span-2 border-t pt-4"><h4 class="text-sm font-bold text-gray-500 uppercase">Dados Bancários</h4></div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Banco</label>
                            <input type="text" name="banco" id="banco" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Agência</label>
                            <input type="text" name="agencia" id="agencia" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700">Conta</label>
                            <input type="text" name="conta" id="conta" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Chave PIX</label>
                            <input type="text" name="chave_pix" id="chave_pix" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>

                         <!-- Comissões -->
                        <div class="col-span-2 border-t pt-4"><h4 class="text-sm font-bold text-gray-500 uppercase">Percentuais Padrão (%)</h4></div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Aluguel</label>
                            <input type="number" step="0.01" name="percentual_comissao_padrao_aluguel" id="pc_aluguel" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Venda</label>
                            <input type="number" step="0.01" name="percentual_comissao_padrao_venda" id="pc_venda" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>
                    </div>

                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">Salvar</button>
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('modal');
    
    function openModal(item = null) {
        document.getElementById('formCorretor').reset();
        document.getElementById('modal-title').innerText = item ? 'Editar Corretor' : 'Novo Corretor';
        
        if (item) {
            document.getElementById('id').value = item.id;
            document.getElementById('nome').value = item.nome;
            document.getElementById('cpf_cnpj').value = item.cpf_cnpj;
            document.getElementById('creci').value = item.creci;
            document.getElementById('email').value = item.email;
            document.getElementById('banco').value = item.banco;
            document.getElementById('agencia').value = item.agencia;
            document.getElementById('conta').value = item.conta;
            document.getElementById('chave_pix').value = item.chave_pix;
            document.getElementById('pc_aluguel').value = item.percentual_comissao_padrao_aluguel;
            document.getElementById('pc_venda').value = item.percentual_comissao_padrao_venda;
        }
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    async function loadData() {
        const res = await fetch('api/crud_corretor.php');
        const data = await res.json();
        const list = document.getElementById('list');
        list.innerHTML = '';

        data.forEach(item => {
            const li = document.createElement('li');
            li.innerHTML = `
                <div class="block hover:bg-gray-50">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 truncate">${item.nome}</p>
                            <div class="ml-2 flex-shrink-0 flex">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    CRECI: ${item.creci || 'N/A'}
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p class="flex items-center text-sm text-gray-500 mr-4">
                                    ${item.email || '-'}
                                </p>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                <button onclick='openModal(${JSON.stringify(item)})' class="text-indigo-600 hover:text-indigo-900 mr-4">Editar</button>
                                <button onclick="deleteItem(${item.id})" class="text-red-600 hover:text-red-900">Excluir</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            list.appendChild(li);
        });
    }

    async function deleteItem(id) {
        if (!confirm('Tem certeza?')) return;
        await fetch(`api/crud_corretor.php?id=${id}`, { method: 'DELETE' });
        loadData();
    }

    document.getElementById('formCorretor').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        
        await fetch('api/crud_corretor.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });
        
        closeModal();
        loadData();
    });

    loadData();
</script>

<?php require_once 'assets/footer.php'; ?>
