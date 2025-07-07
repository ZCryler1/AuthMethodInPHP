CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    two_factor_secret VARCHAR(32) DEFAULT NULL,
    two_factor_enabled BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Índice para otimizar busca por email
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);

CREATE TABLE IF NOT EXISTS login_attempts (
    email VARCHAR(255) PRIMARY KEY,
    attempts INTEGER NOT NULL DEFAULT 0,
    last_attempt DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Índice para otimizar busca por email em login_attempts
CREATE INDEX IF NOT EXISTS idx_login_attempts_email ON login_attempts(email);

