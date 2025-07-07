<?php
require_once __DIR__ . '/2FA.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

/**
 * Sanitiza entrada do usuário removendo espaços em branco e convertendo caracteres especiais para entidades HTML.
 *
 * @param string $data A string de entrada para sanitizar.
 * @return string A string sanitizada.
 */

function sanitizeInput($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Valida o endereço de email usando filtro PHP.
 */
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Valida a senha, garantindo que atenda aos padrões mínimos de segurança.
 *
 * @param string $password A senha a ser validada.
 * @param int $minLength O comprimento mínimo da senha. Padrão é 8.
 * @return bool Retorna true se a senha for válida, caso contrário false.
 */
function validatePassword($password, $minLength = 8)
{
    // Verifica comprimento mínimo
    if (strlen($password) < $minLength) {
        return false;
    }

    // Verifica se tem pelo menos uma letra maiúscula
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }

    // Verifica se tem pelo menos um número
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }

    // Verifica se tem pelo menos um caractere especial
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
        return false;
    }

    return true;
}
/**
 * Verifica se o usuário está logado.
 *
 * @return bool Retorna true se o usuário estiver logado, caso contrário false.
 */
function redirect($location, $message = '')
{
    if ($message) {
        $location .= (strpos($location, '?') !== false ? '&' : '?') . 'error=' . urlencode($message);
    }
    header('Location: ' . $location);
    exit;
}

/**
 * Obtém a mensagem de erro da URL, se existir.
 *
 * @return string A mensagem de erro ou uma string vazia se não houver erro.
 */

function getErrorFromUrl()
{
    return $_GET['error'] ?? '';
}

/**
 * Registra um novo usuário.
 *
 * @param string $email O email do usuário.
 * @param string $password A senha do usuário, que deve atender aos critérios de segurança.
 * @param string $confirmPassword A confirmação da senha do usuário.
 * @return array Retorna um array com o status da operação e uma mensagem.
 */
function registerUser($email, $password, $confirmPassword)
{
    // Validações
    if (!validateEmail($email)) {
        return ['success' => false, 'message' => 'Email inválido'];
    }

    if (!validatePassword($password)) {
        return ['success' => false, 'message' => 'Senha deve ter no mínimo 6 caracteres, uma letra maiúscula, um número e um caractere especial'];
    }

    if ($password !== $confirmPassword) {
        return ['success' => false, 'message' => 'As senhas não coincidem'];
    }

    // Tenta criar o usuário
    if (createUser($email, $password)) {
        // Loga automaticamente após registro
        if (login($email, $password)) {
            return ['success' => true, 'message' => 'Conta criada com sucesso'];
        }
    }

    return ['success' => false, 'message' => 'Erro ao criar conta. Email já pode estar em uso'];
}

/**
 * Ativa o 2FA para um usuário
 */
function enable2FA($userId)
{
    $ga = new GoogleAuthenticator();
    $secret = $ga->generateSecret();

    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE users SET two_factor_secret = ?, two_factor_enabled = 1 WHERE id = ?");

    if ($stmt->execute([$secret, $userId])) {
        return $secret;
    }

    return false;
}

/**
 * Desativa o 2FA para um usuário
 */
function disable2FA($userId)
{
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE users SET two_factor_secret = NULL, two_factor_enabled = 0 WHERE id = ?");

    return $stmt->execute([$userId]);
}

/**
 * Verifica se o usuário tem 2FA ativado
 */
function has2FA($userId)
{
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT two_factor_enabled FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    $result = $stmt->fetch();
    return $result && $result['two_factor_enabled'];
}

/**
 * Obtém o segredo 2FA do usuário
 */
function get2FASecret($userId)
{
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT two_factor_secret FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    $result = $stmt->fetch();
    return $result ? $result['two_factor_secret'] : null;
}

/**
 * Verifica o código 2FA
 */
function verify2FACode($userId, $code)
{
    $secret = get2FASecret($userId);

    if (!$secret) {
        return false;
    }

    $ga = new GoogleAuthenticator();

    // Debug temporário
    if (isset($_GET['debug']) || isset($_POST['debug'])) {
        return debug2FACode($userId, $code);
    }

    return $ga->verifyCode($secret, $code);
}

/**
 * Gera a URL do QR Code para configuração do 2FA
 */
function generate2FAQRCode($userId, $email)
{
    $secret = get2FASecret($userId);

    if (!$secret) {
        return false;
    }

    $ga = new GoogleAuthenticator();
    return $ga->getQRCodeUrl($email, $_SERVER['HTTP_HOST'], $secret, 'Sistema Auth');
}

/**
 * Debug: Verifica código 2FA com log detalhado
 */
function debug2FACode($userId, $code)
{
    $secret = get2FASecret($userId);

    if (!$secret) {
        error_log("2FA Debug: Secret não encontrado para usuário $userId");
        return false;
    }

    $ga = new GoogleAuthenticator();

    // Gera códigos para debug
    $currentTime = time();
    error_log("2FA Debug: Timestamp atual: $currentTime");
    error_log("2FA Debug: Secret: $secret");
    error_log("2FA Debug: Código fornecido: $code");

    for ($i = -2; $i <= 2; $i++) {
        $testTime = $currentTime + ($i * 30);
        $testCode = $ga->getCode($secret, $testTime);
        error_log("2FA Debug: Tempo offset $i (timestamp $testTime): código $testCode");
    }

    $result = $ga->verifyCode($secret, $code);
    error_log("2FA Debug: Resultado da verificação: " . ($result ? 'VÁLIDO' : 'INVÁLIDO'));

    return $result;
}

/* Limite de tentativas de login */