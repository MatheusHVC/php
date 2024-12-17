<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Configurações do banco de dados
$host = 'sql200.infinityfree.com';
$dbname = 'if0_37931394_game';
$username = 'if0_37931394';
$password = 'Dv050397';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro na conexão com o banco de dados: ' . $e->getMessage()
    ]);
    exit;
}

// Recebe os dados do POST
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validação básica
        if (empty($data['email']) || empty($data['password'])) {
            throw new Exception('Email e senha são obrigatórios');
        }

        // Busca o usuário pelo email
        $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            throw new Exception('Email ou senha incorretos');
        }

        // Remove a senha do objeto de resposta
        unset($user['password']);

        echo json_encode([
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'user' => $user
        ]);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?>