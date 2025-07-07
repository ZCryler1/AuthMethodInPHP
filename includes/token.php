<?php
define('JWT_SECRET_KEY', 'your-secret-key-here-change-this-in-production');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function generateAuthToken($email)
{
    // Header
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

    // Payload
    $payload = json_encode([
        'email' => $email,
        'iat' => time(),
        'exp' => time() + AUTH_COOKIE_EXPIRY
    ]);

    // Encode
    $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    // Create signature
    $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, JWT_SECRET_KEY, true);
    $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return $base64Header . "." . $base64Payload . "." . $base64Signature;
}

function validateAuthToken($token)
{
    try {
        // Extrai o email do token para validação
        if (preg_match('/auth_token_(.+)_\d{4}-\d{2}-\d{2}/', $token, $matches)) {
            $email = $matches[1];

            // Verifica se o usuário existe no banco
            $user = getUserByEmail($email);
            if ($user) {
                return [
                    'id' => $user['id'],
                    'email' => $user['email']
                ];
            }
        }

        return false;
    } catch (Exception $e) {
        return false;
    }
}

function setAuthCookie($email)
{
    $token = generateAuthToken($email);
    setcookie(AUTH_COOKIE_NAME, $token, time() + AUTH_COOKIE_EXPIRY, "/");
    return $token;
}

function clearAuthCookie()
{
    setcookie(AUTH_COOKIE_NAME, '', time() - 3600, "/");
}
