<?php
// comissoes.php
require_once 'src/auth.php';
checkAuth();
require_once 'assets/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Gestão de Comissões</h1>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
            <button onclick="switchTab('receber')" id="tab-receber" class="border-indigo-500 text-indigo-600 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">A Receber</button>
            <button onclick="switchTab('pagar')" id="tab-pagar" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">A Pagar</button>
            <button onclick="switchTab('conciliacao')" id="tab-conciliacao" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">Conciliação Bancária</button>
        </nav>
    </div>

    <!-- Content: A Receber -->
    <div id="content-receber">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Comissões Aguardando Recebimento da Venda/Aluguel</h2>
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul id="list-receber" class="divide-y divide-gray-200"></ul>
        </div>
    </div>

    <!-- Content: A Pagar -->
    <div id="content-pagar" class="hidden">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Comissões Pendentes de Pagamento</h2>
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul id="list-pagar" class="divide-y divide-gray-200"></ul>
        </div>
    </div>

    <!-- Content: Conciliação -->
    <div id="content-conciliacao" class="hidden">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Conciliação de Pagamentos</h2>
        <p class="text-sm text-gray-500 mb-4">Selecione uma transação do banco (esquerda) e um pagamento do sistema (direita) para conciliar.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Banco (OFX) -->
            <div class="bg-white shadow sm:rounded-md p-4">
                <h3 class="font-bold text-gray-700 mb-2">Transações Bancárias (OFX)</h3>
                <ul id="list-ofx" class="divide-y divide-gray-200 h-96 overflow-y-auto"></ul>
            </div>
            
            <!-- Sistema (Pagamentos) -->
            <div class="bg-white shadow sm:rounded-md p-4">
                <h3 class="font-bold text-gray-700 mb-2">Pagamentos de Comissão (Sistema)</h3>
                 <ul id="list-sys-pay" class="divide-y divide-gray-200 h-96 overflow-y-auto"></ul>
            </div>
        </div>
        
        <div class="mt-4 flex justify-end">
            <button onclick="doConciliate()" class="bg-indigo-600 text-white px-6 py-2 rounded shadow hover:bg-indigo-700 transition">Conciliar Selecionados</button>
        </div>
    </div>
</div>

<!-- Modal Pagamento -->
<div id="modalPay" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeModalPay()"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="formPay">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Registrar Pagamento</h3>
                    <input type="hidden" name="id_comissao" id="pay_id_comissao">
                    <input type="hidden" name="id_corretor" id="pay_id_corretor">

                    <p class="mb-4 text-sm text-gray-600">Pagando ao corretor: <strong id="pay_nome_corretor"></strong></p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Valor a Pagar</label>
                            <input type="number" step="0.01" name="valor_pago" id="pay_valor" required class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data Pagamento</label>
                            <input type="date" name="data_pagamento" required class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Método</label>
                            <select name="metodo_pagamento" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                <option value="PIX">PIX</option>
                                <option value="TED/DOC">TED/DOC</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Dinheiro">Dinheiro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Referência / Comprovante</label>
                            <input type="text" name="referencia_bancaria" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">Confirmar Pagamento</button>
                    <button type="button" onclick="closeModalPay()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function switchTab(tab) {
        document.querySelectorAll('[id^="content-"]').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('[id^="tab-"]').forEach(el => {
            el.className = "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm";
        });
        document.getElementById('content-' + tab).classList.remove('hidden');
        document.getElementById('tab-' + tab).className = "border-indigo-500 text-indigo-600 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm";
        
        if(tab === 'receber') loadReceber();
        if(tab === 'pagar') loadPagar();
        if(tab === 'conciliacao') loadConciliacao();
    }
    
    // --- Variáveis de Seleção da Conciliação ---
    let selectedOfx = null;
    let selectedSys = null;

    async function loadConciliacao() {
        // 1. Load OFX (Débitos = Saídas, pois pagamos comissão)
        // Precisamos filtrar apenas saídas? Não necessariamente, mas comissões são pagas, então Transações de Débito.
        // O endpoint list_ofx retorna tudo. Vamos filtrar no front por enquanto.
        const resOfx = await fetch('api/list_ofx.php');
        const dataOfx = await resOfx.json();
        
        const listOfx = document.getElementById('list-ofx');
        listOfx.innerHTML = '';
        
        dataOfx.forEach(item => {
            // Filtra visualmente apenas DEBIT se quisermos ser estritos, mas as vezes o OFX vem invertido.
            const li = document.createElement('li');
            li.className = "p-3 hover:bg-gray-100 cursor-pointer border-l-4 border-transparent";
            li.onclick = () => selectItem('ofx', item.id, li);
            li.id = `ofx-${item.id}`;
            li.innerHTML = `
                <div class="flex justify-between">
                    <span class="text-sm font-medium">${item.descricao_original}</span>
                    <span class="text-sm font-bold text-red-600">R$ ${item.valor}</span>
                </div>
                <div class="text-xs text-gray-500">${item.data}</div>
            `;
            listOfx.appendChild(li);
        });

        // 2. Load Pagamentos Sistema
        const resSys = await fetch('api/crud_comissao.php?action=list_payments_reconcile');
        const dataSys = await resSys.json();
        
        const listSys = document.getElementById('list-sys-pay');
        listSys.innerHTML = '';
        
        dataSys.forEach(item => {
            const li = document.createElement('li');
            li.className = "p-3 hover:bg-gray-100 cursor-pointer border-l-4 border-transparent";
            li.onclick = () => selectItem('sys', item.id, li);
            li.id = `sys-${item.id}`;
            li.innerHTML = `
                 <div class="flex justify-between">
                    <span class="text-sm font-medium">Pagto: ${item.corretor_nome}</span>
                    <span class="text-sm font-bold text-red-600">R$ ${item.valor_pago}</span>
                </div>
                <div class="text-xs text-gray-500">${item.data_pagamento}</div>
            `;
            listSys.appendChild(li);
        });
    }

    function selectItem(type, id, el) {
        // Remove active class from siblings
        if(type === 'ofx') {
            selectedOfx = id;
            document.querySelectorAll('#list-ofx li').forEach(l => l.classList.remove('border-indigo-500', 'bg-indigo-50'));
        } else {
            selectedSys = id;
            document.querySelectorAll('#list-sys-pay li').forEach(l => l.classList.remove('border-indigo-500', 'bg-indigo-50'));
        }
        el.classList.add('border-indigo-500', 'bg-indigo-50');
    }

    async function doConciliate() {
        if (!selectedOfx || !selectedSys) {
            return alert('Selecione um item de cada lado!');
        }
        
        if (!confirm('Confirmar conciliação destes itens?')) return;
        
        const res = await fetch('api/reconcile_transaction.php', {
            method: 'POST',
            body: JSON.stringify({
                ofx_id: selectedOfx,
                lancamento_id: selectedSys, // ID do pagamento_comissao
                tipo_lancamento: 'pagamento_comissao',
                action: 'link'
            })
        });
        
        if(res.ok) {
            alert('Conciliado com sucesso!');
            selectedOfx = null;
            selectedSys = null;
            loadConciliacao();
        } else {
            alert('Erro ao conciliar');
        }
    }

    async function loadReceber() {
        const res = await fetch('api/crud_comissao.php?action=list_receber');
        const data = await res.json();
        const list = document.getElementById('list-receber');
        list.innerHTML = '';
        data.forEach(c => {
            list.innerHTML += `
                <li class="px-4 py-4 sm:px-6 border-b border-gray-100 hover:bg-gray-50">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm font-medium text-indigo-600">${c.receita_descricao}</p>
                            <p class="text-xs text-gray-500">Imóvel: ${c.imovel || 'N/A'}</p>
                        </div>
                         <div class="text-right">
                           <p class="text-sm text-gray-900 font-bold">Total: R$ ${parseFloat(c.valor_comissao_total).toFixed(2)}</p>
                           <p class="text-xs text-orange-500">${c.status_comissao}</p>
                        </div>
                    </div>
                </li>
            `;
        });
    }

    async function loadPagar() {
        const res = await fetch('api/crud_comissao.php?action=list_pagar');
        const data = await res.json();
        const list = document.getElementById('list-pagar');
        list.innerHTML = '';
        data.forEach(c => {
            // Logica para separar pagamentos de captador e vendedor
            // Cada linha aqui representa a COMISSÃO. Se tiver 2 corretores, mostra 2 botões?
            // Vamos simplificar listando.
            
            let htmlButtons = '';
            if (parseFloat(c.valor_a_pagar_captador) > 0 && c.captador_nome) {
                htmlButtons += `<button onclick='openModalPay(${c.id}, ${c.id_corretor_captador}, "${c.captador_nome}", ${c.valor_a_pagar_captador})' class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Pagar Captador (${c.captador_nome})</button>`;
            }
            if (parseFloat(c.valor_a_pagar_vendedor) > 0 && c.vendedor_nome) {
                htmlButtons += `<button onclick='openModalPay(${c.id}, ${c.id_corretor_vendedor}, "${c.vendedor_nome}", ${c.valor_a_pagar_vendedor})' class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Pagar Vendedor (${c.vendedor_nome})</button>`;
            }

            list.innerHTML += `
                 <li class="px-4 py-4 sm:px-6 border-b border-gray-100 hover:bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-indigo-600">${c.receita_descricao}</p>
                            <p class="text-xs text-gray-500">Venda: ${c.data_geracao}</p>
                        </div>
                        <div class="flex items-center">
                           ${htmlButtons}
                        </div>
                    </div>
                </li>
            `;
        });
    }

    function openModalPay(id_comissao, id_corretor, nome_corretor, valor) {
        document.getElementById('formPay').reset();
        document.getElementById('pay_id_comissao').value = id_comissao;
        document.getElementById('pay_id_corretor').value = id_corretor;
        document.getElementById('pay_nome_corretor').innerText = nome_corretor;
        document.getElementById('pay_valor').value = parseFloat(valor).toFixed(2);
        
        document.getElementById('modalPay').classList.remove('hidden');
    }

    function closeModalPay() {
        document.getElementById('modalPay').classList.add('hidden');
    }

    document.getElementById('formPay').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        
        const res = await fetch('api/crud_comissao.php?action=pay', {
            method: 'POST',
            body: JSON.stringify(data)
        });
        const json = await res.json();
        
        if (res.ok) {
            alert(json.message);
            closeModalPay();
            loadPagar();
        } else {
            alert(json.error);
        }
    });

    loadReceber();
</script>

<?php require_once 'assets/footer.php'; ?>
