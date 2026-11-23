<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once "conexao.php";

$totalMateriais = $pdo->query(
    "SELECT COUNT(*) FROM materiais"
)->fetchColumn();

$totalEmprestimos = $pdo->query(
    "SELECT COUNT(*) FROM emprestimos
     WHERE status = 'Pendente'"
)->fetchColumn();

$totalAvisos = $pdo->query(
    "SELECT COUNT(*) FROM avisos"
)->fetchColumn();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Painel Principal</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="container">

    <h1>Bem-vindo,
        <?= htmlspecialchars($_SESSION['usuario']) ?>!
    </h1>

    <h2>Painel Principal</h2>

    <p>Materiais cadastrados: <?= $totalMateriais ?></p>

    <p>Empréstimos pendentes: <?= $totalEmprestimos ?></p>

    <p>Avisos publicados: <?= $totalAvisos ?></p>

    <hr>

    <h2>Menu</h2>

    <a href="materiais.php">Ver materiais</a>

    <a href="cadastrar_material.php">Cadastrar material</a>

    <a href="emprestimos.php">Ver empréstimos</a>

    <a href="cadastrar_emprestimos.php">Registrar empréstimo</a>

    <a href="devolver.php">devolução</a>

    <a href="avisos.php">Avisos e eventos</a>

    <a href="logout.php">Sair</a>

</div>

</body>
</html>