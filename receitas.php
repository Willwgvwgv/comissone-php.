<?php
// receitas.php
require_once 'src/auth.php';
checkAuth();
require_once 'assets/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Receitas</h1>
        <button onclick="openModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
            + Nova Receita
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
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="formReceita">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Nova Receita</h3>
                    <input type="hidden" name="id" id="id">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Descrição</label>
                            <input type="text" name="descricao" id="descricao" required class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Valor</label>
                            <input type="number" step="0.01" name="valor" id="valor" required class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data</label>
                            <input type="date" name="data" id="data" required class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Categoria</label>
                            <select name="categoria" id="categoria" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                <option value="Aluguel">Aluguel</option>
                                <option value="Comissão de Venda">Comissão de Venda</option>
                                <option value="Taxa de Administração">Taxa de Administração</option>
                                <option value="Outros">Outros</option>
                            </select>
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700">Imóvel (Opcional)</label>
                            <select name="imovel_id" id="imovel_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                <option value="">Nenhum</option>
                            </select>
                        </div>
                        
                        <!-- Campos de Comissão -->
                         <div class="col-span-full border-t pt-2 mt-2">
                             <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Comissões (Opcional)</h4>
                             <div class="grid grid-cols-2 gap-2">
                                 <div>
                                    <label class="block text-xs text-gray-700">Captador</label>
                                    <select name="id_corretor_captador" id="id_corretor_captador" class="block w-full border border-gray-300 rounded-md py-1 px-2 text-sm">
                                        <option value="">Selecione</option>
                                    </select>
                                    <input type="number" step="0.5" name="pc_comissao_captacao" placeholder="%" class="mt-1 block w-full border border-gray-300 rounded-md py-1 px-2 text-sm">
                                 </div>
                                 <div>
                                    <label class="block text-xs text-gray-700">Vendedor</label>
                                    <select name="id_corretor_vendedor" id="id_corretor_vendedor" class="block w-full border border-gray-300 rounded-md py-1 px-2 text-sm">
                                        <option value="">Selecione</option>
                                    </select>
                                    <input type="number" step="0.5" name="pc_comissao_venda" placeholder="%" class="mt-1 block w-full border border-gray-300 rounded-md py-1 px-2 text-sm">
                                 </div>
                             </div>
                         </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">Salvar</button>
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('modal');
    
    function openModal(item = null) {
        document.getElementById('formReceita').reset();
        document.getElementById('modal-title').innerText = item ? 'Editar Receita' : 'Nova Receita';
        
        if (item) {
            document.getElementById('id').value = item.id;
            document.getElementById('descricao').value = item.descricao;
            document.getElementById('valor').value = item.valor;
            document.getElementById('data').value = item.data;
            document.getElementById('categoria').value = item.categoria;
            document.getElementById('imovel_id').value = item.imovel_id || "";
        }
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    async function loadData() {
        // Load Imoveis for select
        const resImoveis = await fetch('api/crud_imovel.php');
        const dataImoveis = await resImoveis.json();
        const selImovel = document.getElementById('imovel_id');
        // Keep first option
        selImovel.innerHTML = '<option value="">Nenhum</option>';
        dataImoveis.imoveis.forEach(i => {
           const opt = document.createElement('option');
           opt.value = i.id;
           opt.innerText = i.endereco_completo;
           selImovel.appendChild(opt);
        });

        // Load Corretores
        const resCorr = await fetch('api/crud_corretor.php');
        const dataCorr = await resCorr.json();
        const selCap = document.getElementById('id_corretor_captador');
        const selVen = document.getElementById('id_corretor_vendedor');
        
        selCap.innerHTML = '<option value="">Selecione</option>';
        selVen.innerHTML = '<option value="">Selecione</option>';
        
        dataCorr.forEach(c => {
             const opt = `<option value="${c.id}">${c.nome}</option>`;
             selCap.innerHTML += opt;
             selVen.innerHTML += opt;
        });

        // Load Receitas
        const res = await fetch('api/crud_receita.php');
        const data = await res.json();
        const list = document.getElementById('list');
        list.innerHTML = '';

        data.forEach(item => {
            const li = document.createElement('li');
            li.innerHTML = `
                <div class="block hover:bg-gray-50">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 truncate">${item.descricao}</p>
                            <div class="ml-2 flex-shrink-0 flex">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    R$ ${parseFloat(item.valor).toFixed(2)}
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p class="flex items-center text-sm text-gray-500 mr-4">
                                    ${item.categoria}
                                </p>
                                <p class="flex items-center text-sm text-gray-500">
                                    ${item.data.split('-').reverse().join('/')}
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
        await fetch(`api/crud_receita.php?id=${id}`, { method: 'DELETE' });
        loadData();
    }

    document.getElementById('formReceita').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        
        await fetch('api/crud_receita.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });
        
        closeModal();
        loadData();
    });

    loadData();
</script>

<?php require_once 'assets/footer.php'; ?>
