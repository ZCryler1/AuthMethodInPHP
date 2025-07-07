# Sistema de Login usando SQLite e PHP

Este é um exemplo simples de um sistema de login utilizando PHP e SQLite. O sistema permite que usuários se registrem e façam login, armazenando as informações em um banco de dados SQLite.


## Requisitos
- PHP 7.0 ou superior
- Extensão SQLite3 habilitada no PHP
- Servidor web (como Apache ou Nginx) configurado para executar scripts PHP

## Estrutura do Projeto
```
.
├── assets
│   ├── css
│   │   ├── styles.css // Estilos gerais do site
│   │   ├── dashboard.css // Estilos para a página do dashboard
│   │   ├── 2fa.css // Estilos para a página de autenticação de dois fatores
│   │   └── register-style.css // Estilos para a página de registro
│   └── images
├── database
│   ├── database.db
│   ├── init.php // Script para inicializar o banco de dados (feito pelo copilot, não consegui fazer funcionar KKKKKKK)
│   └── schema.sql // Script para criar as tabelas do banco de dados
├── includes
│   ├── config.php // Configurações do banco de dados
│   ├── functions.php // Funções auxiliares
│   ├── db.php // Conexão com o banco de dados
│   ├── token.php // Armazenamento e verificação de tokens (cookies)
│   ├── 2FA.php // Funções para autenticação de dois fatores usando Google Authenticator
│   └── auth.php // Funções de autenticação
├── public
│   ├── index.php // Página de login
│   ├── register.php // Página de registro
│   ├── dashboard.php // Página do dashboard
│   ├── logout.php // Página de logout
│   ├── manage-2fa.php // Página de gerenciamento de autenticação de dois fatores
│   ├── setup-2fa.php // Página de configuração de autenticação de dois fatores
│   └── login.php // Página de verificação de autenticação de dois fatores e login
├── README.md // Este arquivo
└── .gitignore // Arquivos e pastas a serem ignorados pelo Git
```
## Configuração do Banco de Dados
1. Certifique-se de que a extensão SQLite3 está habilitada no seu PHP.
2. Execute o script `init.php` para criar as tabelas necessárias no banco de dados SQLite.
```bash
php database/init.php
```
## Configuração do Servidor Web
Certifique-se de que o servidor web está configurado para apontar para a pasta `public`. Por exemplo, se estiver usando o Apache, você pode configurar o `DocumentRoot` para apontar para a pasta `public`.
## Uso
1. Acesse `http://seu-servidor/index.php` para ver a página de login.
2. Registre um novo usuário acessando `http://seu-servidor/register.php`.
3. Após o registro, faça login na página de login.
4. Após o login, você será redirecionado para o dashboard.
5. Você pode configurar a autenticação de dois fatores acessando `http://seu-servidor/setup-2fa.php`.
6. Após configurar a autenticação de dois fatores, você precisará verificar o código gerado pelo Google Authenticator na página de login.

## Contribuição
Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou pull requests para melhorias ou correções.

## Known Issues
### Com o tempo vou adicionando mais funcionalidades e corrigindo problemas, mas atualmente o sistema tem algumas limitações:
❌ O sistema de login não possui criptografia avançada para senhas (usa `password_hash` e `password_verify`, mas não é o suficiente para produção).
❌ O sistema de login não possui proteção contra SQL Injection (embora use prepared statements, ainda há espaço para melhorias).
❌ O sistema de login não possui proteção contra ataques de força bruta (não há limitação de tentativas de login).
❌ O sistema de login não possui proteção contra Cross-Site Request Forgery (CSRF).
❌ O sistema de login não possui proteção contra Cross-Site Scripting (XSS).
❌ Alguns recursos, como recuperação de senha e verificação de e-mail, não estão implementados.
❌ O sistema de autenticação de dois fatores é básico.





