<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once "conexao.php";

$sql = "SELECT * FROM materiais ORDER BY nome";
$stmt = $pdo->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Materiais</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="container">

    <h1>Materiais cadastrados</h1>

    <a href="painel.php">Voltar ao painel</a>

    <a href="cadastrar_material.php">Novo material</a>

    <table>

        <tr>
            <th>Nome</th>
            <th>Quantidade</th>
        </tr>

        <?php while ($material = $stmt->fetch(PDO::FETCH_ASSOC)): ?>

        <tr>
            <td><?= htmlspecialchars($material['nome']) ?></td>
            <td><?= $material['quantidade'] ?></td>
        </tr>

        <?php endwhile; ?>

    </table>

</div>

</body>
</html>