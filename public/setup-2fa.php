<?php
require_once __DIR__ . '/../includes/functions.php';

// Verifica se está logado
$user = requireAuth();

$message = '';
$qrCodeUrl = '';

// Se o usuário já tem 2FA ativado, redireciona para dashboard
if (has2FA($user['id'])) {
    header('Location: dashboard.php?message=2FA já está ativado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['enable_2fa'])) {
        // Ativa 2FA e gera secret
        $secret = enable2FA($user['id']);

        if ($secret) {
            $qrCodeUrl = generate2FAQRCode($user['id'], $user['email']);
            $message = 'Escaneie o QR Code com o Google Authenticator e digite o código para confirmar.';
        } else {
            $message = 'Erro ao ativar 2FA.';
        }
    } elseif (isset($_POST['verify_code'])) {
        $code = sanitizeInput($_POST['verification_code']);

        if (verify2FACode($user['id'], $code)) {
            header('Location: dashboard.php?message=2FA ativado com sucesso!');
            exit;
        } else {
            $message = 'Código inválido. Tente novamente.';
            $qrCodeUrl = generate2FAQRCode($user['id'], $user['email']);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar 2FA</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <div class="background-circuit">
        <div class="left-top"></div>
        <div class="left-bottom"></div>
        <div class="right-top"></div>
        <div class="right-bottom"></div>
    </div>
    <div class="login-container">
        <div class="login-card">
            <h2>Configurar Autenticação de Dois Fatores (2FA)</h2>

            <?php if ($message): ?>
                <div class="message <?= strpos($message, 'Erro') !== false ? 'error' : 'success' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if (empty($qrCodeUrl)): ?>
                <!-- Primeira etapa: Ativar 2FA -->
                <div class="instructions">
                    <h3>Por que usar 2FA?</h3>
                    <p>A autenticação de dois fatores adiciona uma camada extra de segurança à sua conta. Além da sua senha, você precisará de um código gerado pelo app Google Authenticator.</p>

                    <h4>Como funciona:</h4>
                    <ol>
                        <li>Instale o app <strong>Google Authenticator</strong> no seu celular</li>
                        <li>Clique em "Ativar 2FA" abaixo</li>
                        <li>Escaneie o QR Code que será exibido</li>
                        <li>Digite o código de 6 dígitos para confirmar</li>
                    </ol>
                </div>

                <form method="POST">
                    <div class="input-group" style="flex-direction: column;">
                        <button type="submit" name="enable_2fa" class="login-btn" style="width: 124%;">Ativar 2FA</button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>

            <?php else: ?>
                <!-- Segunda etapa: Escanear QR Code e verificar -->
                <div class="instructions">
                    <h3>Passo 1: Escaneie o QR Code</h3>
                    <p>Abra o app Google Authenticator e escaneie o código abaixo:</p>
                </div>

                <div class="qr-container">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($qrCodeUrl) ?>"
                        alt="QR Code para 2FA" class="qr-code">
                </div>

                <div class="instructions">
                    <h3>Passo 2: Digite o código de verificação</h3>
                    <p>Digite o código de 6 dígitos que aparece no seu app Google Authenticator:</p>
                </div>

                <form method="POST" class="verification-form">
                    <div class="input-group" style="flex-direction: column;">
                        <label for="verification_code">Código de verificação:</label>
                        <input type="text"
                            id="verification_code"
                            name="verification_code"
                            pattern="[0-9]{6}"
                            maxlength="6"
                            placeholder="000000"
                            required>
                    </div>

                    <div class="input-group" style="flex-direction: column;">
                        <button type="submit" name="verify_code" class="btn btn-primary">Verificar e Ativar</button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>