<?php
// db.php
$host = 'localhost';
$db   = 'alertabh';
$user = 'seu_usuario';
$pass = 'sua_senha';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}


<?php
// processa_cadastro.php
require 'db.php';

// Validações básicas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf    = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
    $nome   = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email  = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha  = $_POST['password'];

    if (!$cpf || !$nome || !$email || strlen($senha) < 8) {
        die('Dados inválidos. Volte e tente novamente.');
    }

    // Hash da senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Insere no banco
    $stmt = $pdo->prepare("INSERT INTO usuarios (cpf, nome, email, senha) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$cpf, $nome, $email, $senhaHash]);
        echo '<p>Cadastro realizado com sucesso! <a href="index.html">Voltar</a></p>';
    } catch (\PDOException $e) {
        if ($e->getCode() === '23000') {
            echo '<p>CPF ou e-mail já cadastrados.</p>';
        } else {
            echo '<p>Erro no cadastro: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }
} else {
    header('Location: index.html');
    exit;
}