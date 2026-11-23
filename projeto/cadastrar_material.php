<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once "conexao.php";

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome']);
    $quantidade = (int) $_POST['quantidade'];

    try {

        $sql = "INSERT INTO materiais (nome, quantidade)
                VALUES (?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $quantidade]);

        header("Location: materiais.php");
        exit;

    } catch (PDOException $e) {
        $erro = "Erro ao cadastrar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Material</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="container">

    <h1>Novo material</h1>

    <?php if ($erro != ""): ?>
        <p><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="POST">

        <input type="text" name="nome"
               placeholder="Nome do material" required>

        <input type="number" name="quantidade"
               placeholder="Quantidade" min="0" required>

        <button type="submit">Cadastrar</button>

    </form>

    <a href="painel.php">Voltar ao painel</a>

</div>

</body>
</html>