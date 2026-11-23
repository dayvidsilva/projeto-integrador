<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once "conexao.php";

$erro = "";

// Cadastrar aviso
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = trim($_POST['titulo']);
    $mensagem = trim($_POST['mensagem']);

    if ($titulo != "" && $mensagem != "") {

        $sql = "INSERT INTO avisos (titulo, mensagem)
                VALUES (?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titulo, $mensagem]);

        header("Location: avisos.php");
        exit;

    } else {
        $erro = "Preencha todos os campos.";
    }
}

// Listar avisos
$sql = "SELECT * FROM avisos ORDER BY data_criacao DESC";
$stmt = $pdo->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Avisos e Eventos</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="container">

    <h1>Avisos e Eventos</h1>

    <a href="painel.php">Voltar ao painel</a>

    <h2>Novo aviso</h2>

    <?php if ($erro != ""): ?>
        <p><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="POST">

        <input type="text" name="titulo"
               placeholder="Título do aviso" required>

        <textarea name="mensagem"
                  placeholder="Mensagem do aviso"
                  required></textarea>

        <button type="submit">Publicar aviso</button>

    </form>

    <h2>Avisos publicados</h2>

    <?php while ($aviso = $stmt->fetch(PDO::FETCH_ASSOC)): ?>

        <div class="aviso">

            <h3><?= htmlspecialchars($aviso['titulo']) ?></h3>

            <p><?= nl2br(htmlspecialchars($aviso['mensagem'])) ?></p>

            <small>
                <?= htmlspecialchars($aviso['data_criacao']) ?>
            </small>

        </div>

    <?php endwhile; ?>

</div>

</body>
</html>