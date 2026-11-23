<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once "conexao.php";

$erro = "";

// Busca os materiais cadastrados
$sql = "SELECT * FROM materiais WHERE quantidade > 0 ORDER BY nome";
$stmt = $pdo->query($sql);
$materiais = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cadastra o empréstimo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $aluno = trim($_POST['aluno']);
    $material_id = (int) $_POST['material_id'];

    try {

        $pdo->beginTransaction();

        // Verifica a quantidade disponível
        $sql = "SELECT * FROM materiais
                WHERE id = ? FOR UPDATE";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$material_id]);

        $material = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$material || $material['quantidade'] < 1) {

            throw new Exception("Material indisponível.");

        }

        // Registra o empréstimo
        $sql = "INSERT INTO emprestimos
                (aluno, material_id, data_emprestimo, status)
                VALUES (?, ?, CURDATE(), 'Pendente')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$aluno, $material_id]);

        // Atualiza o estoque
        $sql = "UPDATE materiais
                SET quantidade = quantidade - 1
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$material_id]);

        $pdo->commit();

        header("Location: emprestimos.php");
        exit;

    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $erro = $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Empréstimo</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="container">

    <h1>Novo Empréstimo</h1>

    <?php if ($erro != ""): ?>
        <p><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="POST">

        <input
            type="text"
            name="aluno"
            placeholder="Nome do aluno"
            required
        >

        <select name="material_id" required>

            <option value="">Selecione um material</option>

            <?php foreach ($materiais as $material): ?>

                <option value="<?= $material['id'] ?>">
                    <?= htmlspecialchars($material['nome']) ?>
                    (<?= $material['quantidade'] ?> disponíveis)
                </option>

            <?php endforeach; ?>

        </select>

        <button type="submit">Registrar Empréstimo</button>

    </form>

    <a href="painel.php">Voltar ao painel</a>

</div>

</body>
</html>