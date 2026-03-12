# ImobiFinancePro

Sistema financeiro para imobiliárias e corretores com suporte a conciliação bancária (OFX) e PWA.

## 🚀 Instalação

### 1. Requisitos
- Servidor PHP (XAMPP, WAMP, Docker, etc.)
- Banco de Dados MySQL

### 2. Configuração do Banco de Dados
1. Crie um banco de dados no MySQL chamado `imobifinancepro` (ou outro nome de sua preferência).
2. Importe o arquivo `database.sql` incluído na raiz deste projeto.

### 3. Configuração do Projeto
1. Abra o arquivo `src/db.php` e configure as credenciais do seu banco de dados:
   ```php
   $host = 'localhost';
   $dbname = 'imobifinancepro';
   $username = 'root'; // Seu usuário
   $password = ''; // Sua senha
   ```

### 4. Executando
Coloque a pasta do projeto no diretório do seu servidor web (ex: `htdocs` no XAMPP) e acesse via navegador:
`http://localhost/CRM Financeiro/`

## 📱 PWA (App Mobile)
O sistema é compatível com PWA. Para instalar:
1. Acesse o sistema pelo navegador do celular (Chrome/Edge/Safari).
2. No menu do navegador, selecione "Adicionar à Tela Inicial" ou "Instalar Aplicativo".

## 🛠 Tecnologias
- **Backend**: PHP 
- **Frontend**: HTML5, TailwindCSS (CDN), JavaScript Vanilla
- **Banco de Dados**: MySQL
- **Conciliação**: Importação de arquivos OFX

## 📂 Estrutura de Pastas
- `api/`: Endpoints REST para comunicação frontend-backend.
- `src/`: Lógica de conexão, autenticação e parsers.
- `assets/`: Imagens e ícones.
