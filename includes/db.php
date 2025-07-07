<?php
require_once __DIR__ . '/config.php';

/**
 * Obtém uma conexão com o banco de dados SQLite.
 *
 * @return PDO Retorna uma instância PDO conectada ao banco de dados.
 * PDO se refere a PHP Data Objects, uma extensão do PHP que fornece uma interface consistente para acessar bancos de dados.
 * @throws Exception Se houver um erro na conexão com o banco de dados.
 * Exception é uma classe base para todas as exceções do PHP, usada para capturar erros e condições excepcionais.
 */
function getDBConnection()
{
    try {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Ativa o modo WAL para evitar lock
        $pdo->exec('PRAGMA journal_mode = WAL;');
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception('Erro na conexão com o banco de dados: ' . $e->getMessage());
    }
}

function initializeDatabase()
{
    try {
        $pdo = getDBConnection();

        if (file_exists(DB_SCHEMA_PATH)) {
            $schema = file_get_contents(DB_SCHEMA_PATH);
            $pdo->exec($schema);
        }

        return true;
    } catch (Exception $e) {
        return false;
    }
}

function getUserByEmail($email)
{
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, email, password, two_factor_secret, two_factor_enabled FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return false;
    }
}

function createUser($email, $password)
{
    try {
        initializeDatabase();
        $pdo = getDBConnection();

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        return $stmt->execute([$email, $hashedPassword]);
    } catch (Exception $e) {
        return false;
    }
}
