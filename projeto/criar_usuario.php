<?php

require_once "conexao.php";

$nome = "Administrador";
$email = "admin@email.com";
$senha = password_hash("123456", PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nome, email, senha)
        VALUES (?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$nome, $email, $senha]);

echo "Usuário criado!";