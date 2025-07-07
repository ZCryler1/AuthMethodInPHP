<?php
try {
    // Caminho para o banco de dados
    $dbPath = __DIR__ . '/database.db';
    $schemaPath = __DIR__ . '/schema.sql';

    // Remove o arquivo do banco se estiver corrompido
    if (file_exists($dbPath)) {
        unlink($dbPath);
        echo "🗑️ Arquivo de banco corrompido removido.\n";
    }

    // Conecta ao banco de dados SQLite (cria o arquivo se não existir)
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Lê e executa o schema SQL
    if (file_exists($schemaPath)) {
        $schema = file_get_contents($schemaPath);
        $pdo->exec($schema);
        echo "✅ Banco de dados criado com sucesso!\n";
        echo "📁 Localização: $dbPath\n";
    } else {
        echo "❌ Arquivo schema.sql não encontrado em: $schemaPath\n";
        exit(1);
    }

    // Verifica se a tabela foi criada
    $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'")->fetch();
    if ($result) {
        echo "✅ Tabela 'users' criada com sucesso!\n";
    } else {
        echo "❌ Falha ao criar tabela 'users'\n";
        exit(1);
    }

    // Conta quantos usuários existem
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "👥 Usuários cadastrados: $count\n";
    echo "🎉 Banco de dados configurado e pronto para uso!\n";
} catch (PDOException $e) {
    echo "❌ Erro ao criar banco de dados: " . $e->getMessage() . "\n";
    echo "💡 Sugestão: Verifique as permissões da pasta e tente novamente.\n";
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
}
