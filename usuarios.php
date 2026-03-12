<?php
// usuarios.php
require_once 'src/auth.php';
checkAdmin();
require_once 'assets/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Usuários do Sistema</h1>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <ul id="user-list" class="divide-y divide-gray-200">
            <!-- JS -->
        </ul>
    </div>
</div>

<script>
    async function loadUsers() {
        const res = await fetch('api/list_usuarios.php');
        const users = await res.json();
        const list = document.getElementById('user-list');
        list.innerHTML = '';
        
        users.forEach(u => {
            const li = document.createElement('li');
            li.innerHTML = `
                <div class="px-4 py-4 sm:px-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-indigo-600">${u.nome}</p>
                        <p class="text-sm text-gray-500">${u.email}</p>
                    </div>
                    <div>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${u.role==='admin'?'bg-purple-100 text-purple-800':'bg-gray-100 text-gray-800'}">
                            ${u.role}
                        </span>
                    </div>
                </div>
            `;
            list.appendChild(li);
        });
    }
    loadUsers();
</script>

<?php require_once 'assets/footer.php'; ?>
