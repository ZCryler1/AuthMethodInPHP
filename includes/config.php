<?php
// Configurações do banco de dados
define('DB_PATH', __DIR__ . '/../database/database.db');
define('DB_SCHEMA_PATH', __DIR__ . '/../database/schema.sql');

// Configurações de autenticação
define('AUTH_COOKIE_NAME', 'auth_token');
define('AUTH_COOKIE_EXPIRY', 86400 * 30); // 30 dias
define('SESSION_NAME', 'user');

// Configurações gerais
define('REDIRECT_AFTER_LOGIN', 'dashboard.php');
define('REDIRECT_AFTER_LOGOUT', 'index.php');
define('REDIRECT_IF_NOT_AUTHENTICATED', 'index.php');
