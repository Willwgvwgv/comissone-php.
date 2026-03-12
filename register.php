<?php
// register.php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - ImobiFinancePro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center px-4">

    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Crie sua Conta</h1>
        </div>

        <form id="registerForm" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
                <input type="text" name="nome" required class="mt-1 block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" required class="mt-1 block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Senha</label>
                <input type="password" name="senha" required class="mt-1 block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cadastrar
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">Já tem conta? <a href="index.php" class="font-medium text-indigo-600 hover:text-indigo-500">Faça login</a></p>
        </div>

        <div id="msg" class="mt-4 text-center text-sm font-medium"></div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            try {
                const res = await fetch('api/register.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const json = await res.json();
                
                if (res.ok) {
                    document.getElementById('msg').className = 'mt-4 text-center text-sm font-medium text-green-600';
                    document.getElementById('msg').innerText = json.message + ' Redirecionando...';
                    setTimeout(() => window.location.href = 'index.php', 2000);
                } else {
                    document.getElementById('msg').className = 'mt-4 text-center text-sm font-medium text-red-600';
                    document.getElementById('msg').innerText = json.error;
                }
            } catch (err) {
                console.error(err);
            }
        });
    </script>
</body>
</html>
