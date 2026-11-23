<?php

session_start();
require_once "conexao.php";

$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario && password_verify($senha, $usuario['senha'])) {

    session_regenerate_id(true);

    $_SESSION['usuario'] = $usuario['nome'];
    $_SESSION['id'] = $usuario['id'];

    header("Location: painel.php");
    exit;

} else {

    echo "E-mail ou senha incorretos.";
}