<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/token.php';
require_once __DIR__ . '/db.php';

function isAuthenticated()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verifica se há cookie de autenticação
    if (isset($_COOKIE[AUTH_COOKIE_NAME])) {
        $user = validateAuthToken($_COOKIE[AUTH_COOKIE_NAME]);
        if ($user) {
            $_SESSION[SESSION_NAME] = $user;
            return $user;
        }
    }

    // Verifica se há sessão ativa
    if (isset($_SESSION[SESSION_NAME]) && is_array($_SESSION[SESSION_NAME])) {
        return $_SESSION[SESSION_NAME];
    }

    return false;
}

function login($email, $password, $twoFactorCode = null)
{
    $user = getUserByEmail($email);

    if (!$user) {
        return 'invalid_credentials';
    }

    if (!password_verify($password, $user['password'])) {
        return 'invalid_credentials';
    }

    // Verifica se o usuário tem 2FA ativado
    if ($user['two_factor_enabled']) {
        // Se 2FA está ativado mas código não foi fornecido
        if ($twoFactorCode === null || $twoFactorCode === '') {
            return 'require_2fa';
        }

        // Verifica o código 2FA
        if (!verify2FACode($user['id'], $twoFactorCode)) {
            return 'invalid_2fa';
        }
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION[SESSION_NAME] = [
        'id' => $user['id'],
        'email' => $user['email']
    ];

    setAuthCookie($email);
    return true;
}

function logout()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    unset($_SESSION[SESSION_NAME]);
    session_destroy();
    clearAuthCookie();
}

function requireAuth()
{
    $user = isAuthenticated();
    if (!$user) {
        header('Location: ' . REDIRECT_IF_NOT_AUTHENTICATED);
        exit;
    }
    return $user;
}

function redirectIfAuthenticated()
{
    if (isAuthenticated()) {
        header('Location: ' . REDIRECT_AFTER_LOGIN);
        exit;
    }
}
