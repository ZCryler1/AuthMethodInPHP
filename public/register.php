<?php
require_once __DIR__ . '/../includes/functions.php';

// Se já estiver logado, redireciona para dashboard
redirectIfAuthenticated();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $result = registerUser($email, $password, $confirmPassword);

    if ($result['success']) {
        header('Location: dashboard.php');
        exit;
    } else {
        header('Location: register.php?error=' . urlencode($result['message']));
        exit;
    }
}

$error = getErrorFromUrl();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crie uma conta</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href='data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><circle cx="20" cy="20" r="18" fill="%23222" stroke="%23444" stroke-width="2"/><circle cx="20" cy="20" r="8" fill="none" stroke="%230070f3" stroke-width="3"/></svg>'>
    <style>
        .requirement {
            color: #888;
            transition: color 0.2s;
        }

        .requirement.met {
            color: #2ecc40;
            font-weight: bold;
        }
    </style>
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
            <div class="login-logo">
                <span class="logo-detail left-above">
                    <span class="dot small"></span>
                    <span class="dot medium"></span>
                    <span class="dot large"></span>
                </span>
                <span class="logo-detail left">
                    <span class="dot small"></span>
                    <span class="dot medium"></span>
                    <span class="dot large"></span>
                </span>
                <span class="logo-detail left-down">
                    <span class="dot small"></span>
                    <span class="dot medium"></span>
                    <span class="dot large"></span>
                </span>

                <span class="logo-circle">
                    <!-- Ícone ou logo central -->
                    <svg width="40" height="40" viewBox="0 0 40 40">
                        <circle cx="20" cy="20" r="18" fill="#222" stroke="#444" stroke-width="2" />
                        <circle cx="20" cy="20" r="8" fill="none" stroke="#0070f3" stroke-width="3" />
                    </svg>
                </span>
                <span class="logo-detail right-above">
                    <span class="dot large"></span>
                    <span class="dot medium"></span>
                    <span class="dot small"></span>

                </span>
                <span class="logo-detail right">
                    <span class="dot large"></span>
                    <span class="dot medium"></span>
                    <span class="dot small"></span>
                </span>
                <span class="logo-detail right-down">
                    <span class="dot large"></span>
                    <span class="dot medium"></span>
                    <span class="dot small"></span>
                </span>
            </div>
            <h2>Crie sua conta</h2>
            <p class="signup-link">Já tem uma conta? <a href="index.php">Faça login!</a></p>
            <form action="register.php" method="POST">
                <div class="input-group">
                    <i class="fa fa-envelope"></i><input type="email" id="email" name="email" placeholder="email address" required>
                </div>
                <div class="input-group">
                    <i class="fa fa-lock"></i><input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <i class="fa fa-lock"></i><input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                </div>
                <div class="requirements">
                    <p class="requirements-text">Senha deve ter:</p>
                    <ul>
                        <li class="requirement" id="length-req">8 caracteres</li>
                        <li class="requirement" id="uppercase-req">1 letra maiúscula</li>
                        <li class="requirement" id="number-req">1 número</li>
                        <li class="requirement" id="special-req">1 caractere especial</li>
                    </ul>
                </div>
                <button type="submit" class="login-btn">Criar Conta</button>
                <div class="or-divider">OU</div>
                <div class="social-buttons">
                    <button type="button" class="social-btn apple">
                        <span><svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 50 50">
                                <path d="M 44.527344 34.75 C 43.449219 37.144531 42.929688 38.214844 41.542969 40.328125 C 39.601563 43.28125 36.863281 46.96875 33.480469 46.992188 C 30.46875 47.019531 29.691406 45.027344 25.601563 45.0625 C 21.515625 45.082031 20.664063 47.03125 17.648438 47 C 14.261719 46.96875 11.671875 43.648438 9.730469 40.699219 C 4.300781 32.429688 3.726563 22.734375 7.082031 17.578125 C 9.457031 13.921875 13.210938 11.773438 16.738281 11.773438 C 20.332031 11.773438 22.589844 13.746094 25.558594 13.746094 C 28.441406 13.746094 30.195313 11.769531 34.351563 11.769531 C 37.492188 11.769531 40.8125 13.480469 43.1875 16.433594 C 35.421875 20.691406 36.683594 31.78125 44.527344 34.75 Z M 31.195313 8.46875 C 32.707031 6.527344 33.855469 3.789063 33.4375 1 C 30.972656 1.167969 28.089844 2.742188 26.40625 4.78125 C 24.878906 6.640625 23.613281 9.398438 24.105469 12.066406 C 26.796875 12.152344 29.582031 10.546875 31.195313 8.46875 Z"></path>
                            </svg></span>
                    </button>
                    <button type="button" class="social-btn google">
                        <span><svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 48 48">
                                <path fill="#fbc02d" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12	s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24s8.955,20,20,20	s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path>
                                <path fill="#e53935" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039	l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path>
                                <path fill="#4caf50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36	c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path>
                                <path fill="#1565c0" d="M43.611,20.083L43.595,20L42,20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571	c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
                            </svg></span>
                    </button>
                    <button type="button" class="social-btn x">
                        <span><svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 50 50">
                                <path d="M 5.9199219 6 L 20.582031 27.375 L 6.2304688 44 L 9.4101562 44 L 21.986328 29.421875 L 31.986328 44 L 44 44 L 28.681641 21.669922 L 42.199219 6 L 39.029297 6 L 27.275391 19.617188 L 17.933594 6 L 5.9199219 6 z M 9.7167969 8 L 16.880859 8 L 40.203125 42 L 33.039062 42 L 9.7167969 8 z"></path>
                            </svg></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mostra mensagem de erro se existir
        <?php if ($error): ?>
            const errorMessage = document.createElement('div');
            errorMessage.className = 'error-message';
            errorMessage.textContent = <?php echo json_encode($error); ?>;
            document.querySelector('.login-card').prepend(errorMessage);

            const style = document.createElement('style');
            style.textContent = `
            .error-message {
                color: red;
                margin-bottom: 10px;
                font-weight: bold;
            }
        `;
            document.head.appendChild(style);
        <?php endif; ?>

        window.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const lengthReq = document.getElementById('length-req');
            const uppercaseReq = document.getElementById('uppercase-req');
            const numberReq = document.getElementById('number-req');
            const specialReq = document.getElementById('special-req');

            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const value = passwordInput.value;

                    // 8 caracteres
                    if (value.length >= 8) {
                        lengthReq.classList.add('met');
                    } else {
                        lengthReq.classList.remove('met');
                    }

                    // 1 letra maiúscula
                    if (/[A-Z]/.test(value)) {
                        uppercaseReq.classList.add('met');
                    } else {
                        uppercaseReq.classList.remove('met');
                    }

                    // 1 número
                    if (/\d/.test(value)) {
                        numberReq.classList.add('met');
                    } else {
                        numberReq.classList.remove('met');
                    }

                    // 1 caractere especial
                    if (/[^A-Za-z0-9]/.test(value)) {
                        specialReq.classList.add('met');
                    } else {
                        specialReq.classList.remove('met');
                    }
                });
            }
            // Inicializa os requisitos escondidos
            const requirementsP = document.getElementsByClassName('requirements-text');
            const requirements = document.querySelectorAll('.requirement');
            const confirmPassword = document.getElementById('confirm_password');

            // Esconde os requisitos inicialmente
            requirements.forEach(req => {
                req.style.display = 'none';
                requirementsP[0].style.display = 'none';
            });
            // Exibe os requisitos quando o campo da senha possuir valor
            passwordInput.addEventListener('focus', function() {
                requirements.forEach(req => {
                    req.style.display = 'block';
                    requirementsP[0].style.display = 'block';

                });
            });
        });
    </script>
</body>

</html>