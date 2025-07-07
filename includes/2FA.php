<?php

/**
 * Classe para autenticação de dois fatores usando Google Authenticator
 * Implementação corrigida e compatível
 */
class GoogleAuthenticator
{
    /**
     * Gera uma chave secreta aleatória
     */
    public function generateSecret($length = 16)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $secret;
    }

    /**
     * Gera o código TOTP baseado na chave secreta
     */
    public function getCode($secret, $timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        }

        // Converte para base32
        $key = $this->base32Decode($secret);
        if ($key === false) {
            throw new Exception('Erro na decodificação Base32');
        }

        // Calcula o time step (30 segundos)
        $timeSlice = floor($timestamp / 30);

        // Converte para binary
        $timeSlice = pack('N*', 0, $timeSlice);

        // Gera HMAC-SHA1
        $hash = hash_hmac('sha1', $timeSlice, $key, true);

        // Extrai o código de 6 dígitos
        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset + 0]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;

        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verifica se o código fornecido é válido
     */
    public function verifyCode($secret, $code, $tolerance = 2)
    {
        $timestamp = time();

        // Verifica o código atual e dentro da tolerância
        for ($i = -$tolerance; $i <= $tolerance; $i++) {
            $testTime = $timestamp + ($i * 30);
            if ($this->getCode($secret, $testTime) === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gera URL para QR Code do Google Authenticator
     */
    public function getQRCodeUrl($user, $hostname, $secret, $title = null)
    {
        $title = $title ?: $hostname;
        $label = $user . '@' . $hostname;

        $url = 'otpauth://totp/' . urlencode($label)
            . '?secret=' . $secret
            . '&issuer=' . urlencode($title);

        return $url;
    }

    /**
     * Decodifica base32 (implementação corrigida e robusta)
     */
    private function base32Decode($secret)
    {
        // Remove espaços e converte para maiúsculo
        $secret = strtoupper(str_replace(' ', '', $secret));

        // Remove padding
        $secret = rtrim($secret, '=');

        // Tabela de caracteres Base32
        $table = [
            'A' => 0,
            'B' => 1,
            'C' => 2,
            'D' => 3,
            'E' => 4,
            'F' => 5,
            'G' => 6,
            'H' => 7,
            'I' => 8,
            'J' => 9,
            'K' => 10,
            'L' => 11,
            'M' => 12,
            'N' => 13,
            'O' => 14,
            'P' => 15,
            'Q' => 16,
            'R' => 17,
            'S' => 18,
            'T' => 19,
            'U' => 20,
            'V' => 21,
            'W' => 22,
            'X' => 23,
            'Y' => 24,
            'Z' => 25,
            '2' => 26,
            '3' => 27,
            '4' => 28,
            '5' => 29,
            '6' => 30,
            '7' => 31
        ];

        $binaryString = '';
        $buffer = 0;
        $bitsLeft = 0;

        for ($i = 0; $i < strlen($secret); $i++) {
            $char = $secret[$i];

            if (!isset($table[$char])) {
                return false; // Caractere inválido
            }

            $buffer = ($buffer << 5) | $table[$char];
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $binaryString .= chr(($buffer >> ($bitsLeft - 8)) & 0xFF);
                $bitsLeft -= 8;
            }
        }

        return $binaryString;
    }
}

// Exemplo de uso:
/*
$ga = new GoogleAuthenticator();

// 1. Gerar uma nova chave secreta para o usuário
$secret = $ga->generateSecret();
echo "Chave secreta: " . $secret . "\n";

// 2. Gerar URL para QR Code
$qrUrl = $ga->getQRCodeUrl('usuario@email.com', 'MeuSite.com', $secret);
echo "URL QR Code: " . $qrUrl . "\n";

// 3. Gerar código atual
$currentCode = $ga->getCode($secret);
echo "Código atual: " . $currentCode . "\n";

// 4. Verificar código
$isValid = $ga->verifyCode($secret, $currentCode);
echo "Código válido: " . ($isValid ? 'Sim' : 'Não') . "\n";
*/
