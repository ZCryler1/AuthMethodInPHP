<?php
require_once __DIR__ . '/../includes/functions.php';

$error = '';
$require2FA = false;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $twoFactorCode = sanitizeInput($_POST['two_factor_code'] ?? '');

    $loginResult = login($email, $password, $twoFactorCode ?: null);

    if ($loginResult === true) {
        header('Location: dashboard.php');
        exit;
    } elseif ($loginResult === 'require_2fa') {
        $require2FA = true;
    } elseif ($loginResult === 'invalid_2fa') {
        $error = 'Código 2FA inválido. Verifique o código no seu app Google Authenticator.';
    } elseif ($loginResult === 'invalid_credentials') {
        $error = 'Email ou senha incorretos.';
    } elseif (strpos($loginResult, 'blocked:') === 0) {
        $remainingTime = explode(':', $loginResult)[1];
        $error = "Muitas tentativas de login. Tente novamente em $remainingTime minuto(s).";
    } else {
        $error = 'Erro no login. Tente novamente.';
    }
}

// Verifica se já está logado
redirectIfAuthenticated();

// Se chegou até aqui, redireciona para index
if (!$require2FA) {
    header('Location: index.php' . ($error ? '?error=' . urlencode($error) : ''));
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código 2FA - Login</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <h2>🔐 Autenticação de Dois Fatores</h2>

            <?php if ($error): ?>
                <div class="message error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <p class="signup-link">Digite o código de 6 dígitos do seu app Google Authenticator:</p>

            <form method="POST">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <input type="hidden" name="password" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>">

                <div class="input-group" style="flex-direction: column;">
                    <label for="two_factor_code" style="margin-bottom: 10px;">Código 2FA:</label>
                    <input type="text"
                        id="two_factor_code"
                        name="two_factor_code"
                        pattern="[0-9]{6}"
                        maxlength="6"
                        placeholder="000000"
                        required
                        autofocus>
                </div>

                <div class="input-group" style="flex-direction: column;">
                    <button type="submit" class="login-btn">Verificar</button><br>
                    <a href="index.php" class="btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>