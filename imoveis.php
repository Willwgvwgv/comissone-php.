<?php
// imoveis.php
require_once 'src/auth.php';
checkAuth();
require_once 'assets/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Imóveis</h1>
        <button onclick="openModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
            + Novo Imóvel
        </button>
    </div>

    <!-- Lista de Imóveis -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul id="imoveis-list" class="divide-y divide-gray-200">
            <!-- JS vai popular aqui -->
        </ul>
    </div>
</div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="formImovel">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Novo Imóvel</h3>
                    <input type="hidden" name="id" id="imovel_id">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Endereço Completo</label>
                            <input type="text" name="endereco" id="endereco" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Proprietário Existente</label>
                            <select name="proprietario_id" id="proprietario_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Selecione ou crie novo abaixo</option>
                            </select>
                        </div>

                        <div class="border-t pt-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Ou Novo Proprietário</label>
                            <div class="grid grid-cols-1 gap-3">
                                <input type="text" name="novo_proprietario_nome" placeholder="Nome" class="block w-full border border-gray-300 rounded-md py-1 px-3 text-sm">
                                <input type="email" name="novo_proprietario_email" placeholder="Email (opcional)" class="block w-full border border-gray-300 rounded-md py-1 px-3 text-sm">
                                <input type="text" name="novo_proprietario_telefone" placeholder="Telefone (opcional)" class="block w-full border border-gray-300 rounded-md py-1 px-3 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="Disponível">Disponível</option>
                                <option value="Alugado">Alugado</option>
                                <option value="Venda">Venda</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Salvar</button>
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('modal');
    
    function openModal(imovel = null) {
        document.getElementById('formImovel').reset();
        document.getElementById('modal-title').innerText = imovel ? 'Editar Imóvel' : 'Novo Imóvel';
        
        if (imovel) {
            document.getElementById('imovel_id').value = imovel.id;
            document.getElementById('endereco').value = imovel.endereco_completo;
            document.getElementById('proprietario_id').value = imovel.proprietario_id;
            document.getElementById('status').value = imovel.status;
        }
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    async function loadImoveis() {
        const res = await fetch('api/crud_imovel.php');
        const data = await res.json();
        const list = document.getElementById('imoveis-list');
        const selectProp = document.getElementById('proprietario_id');
        
        list.innerHTML = '';
        selectProp.innerHTML = '<option value="">Selecione...</option>';

        // Populate Select
        data.proprietarios.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.innerText = p.nome;
            selectProp.appendChild(opt);
        });

        // List
        data.imoveis.forEach(imovel => {
            const li = document.createElement('li');
            li.innerHTML = `
                <div class="block hover:bg-gray-50">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 truncate">${imovel.endereco_completo}</p>
                            <div class="ml-2 flex-shrink-0 flex">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    ${imovel.status}
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p class="flex items-center text-sm text-gray-500">
                                    Proprietário: ${imovel.proprietario_nome}
                                </p>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                <button onclick='openModal(${JSON.stringify(imovel)})' class="text-indigo-600 hover:text-indigo-900 mr-4">Editar</button>
                                <button onclick="deleteImovel(${imovel.id})" class="text-red-600 hover:text-red-900">Excluir</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            list.appendChild(li);
        });
    }

    async function deleteImovel(id) {
        if (!confirm('Tem certeza?')) return;
        await fetch(`api/crud_imovel.php?id=${id}`, { method: 'DELETE' });
        loadImoveis();
    }

    document.getElementById('formImovel').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        
        await fetch('api/crud_imovel.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });
        
        closeModal();
        loadImoveis();
    });

    loadImoveis();
</script>

<?php require_once 'assets/footer.php'; ?>
