<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once "conexao.php";

// PROCESSAR DEVOLUÇÃO E EXCLUSÃO

if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_POST['excluir_emprestimo'])) {

    $id = filter_input(
        INPUT_POST,
        'excluir_emprestimo',
        FILTER_VALIDATE_INT
    );

    if (!$id) {
        die("ID inválido!");
    }

    try {

        $pdo->beginTransaction();

        // Buscar empréstimo
        $sql = "SELECT material_id, status
                FROM emprestimos
                WHERE id = ?
                FOR UPDATE";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        $emprestimo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$emprestimo) {
            throw new Exception("Empréstimo não encontrado!");
        }

        // Verificar se está pendente
        if (strtolower(trim($emprestimo['status'])) != 'pendente') {
            throw new Exception("Este empréstimo não está pendente!");
        }

        // Devolver quantidade ao estoque
        $sql = "UPDATE materiais
                SET quantidade = quantidade + 1
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$emprestimo['material_id']]);

        if ($stmt->rowCount() == 0) {
            throw new Exception("Material não encontrado!");
        }

        // Excluir empréstimo
        $sql = "DELETE FROM emprestimos WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        $pdo->commit();

        header("Location: emprestimos.php?sucesso=1");
        exit;

    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        die("Erro: " . htmlspecialchars($e->getMessage()));
    }
}

// LISTAR EMPRÉSTIMOS

$sql = "SELECT emprestimos.*, materiais.nome AS material
        FROM emprestimos
        INNER JOIN materiais
        ON emprestimos.material_id = materiais.id
        ORDER BY emprestimos.id DESC";

$stmt = $pdo->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Empréstimos</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="container">

    <h1>Empréstimos registrados</h1>

    <?php if (isset($_GET['sucesso'])): ?>
        <p style="color: green;">
            Material devolvido e empréstimo excluído com sucesso!
        </p>
    <?php endif; ?>

    <a href="painel.php">Voltar ao painel</a>

    <a href="cadastrar_emprestimo.php">
        Novo empréstimo
    </a>

    <table>

        <tr>
            <th>Aluno</th>
            <th>Material</th>
            <th>Data do empréstimo</th>
            <th>Data da devolução</th>
            <th>Status</th>
            <th>Ação</th>
        </tr>

        <?php while ($emprestimo = $stmt->fetch(PDO::FETCH_ASSOC)): ?>

        <tr>

            <td>
                <?= htmlspecialchars($emprestimo['aluno']) ?>
            </td>

            <td>
                <?= htmlspecialchars($emprestimo['material']) ?>
            </td>

            <td>
                <?= htmlspecialchars($emprestimo['data_emprestimo']) ?>
            </td>

            <td>
                <?= htmlspecialchars($emprestimo['data_devolucao'] ?? 'Pendente') ?>
            </td>

            <td>
                <?= htmlspecialchars($emprestimo['status']) ?>
            </td>

            <td>

                <?php if (strtolower(trim($emprestimo['status'])) == 'pendente'): ?>

                    <form method="POST"
                          action="emprestimos.php"
                          onsubmit="return confirm('Confirma a devolução e exclusão deste empréstimo?');">

                        <input type="hidden"
                               name="excluir_emprestimo"
                               value="<?= $emprestimo['id'] ?>">

                        <button type="submit">
                            Excluir / Devolver
                        </button>

                    </form>

                <?php else: ?>

                    Material já devolvido

                <?php endif; ?>

            </td>

        </tr>

        <?php endwhile; ?>

    </table>

</div>

</body>
</html>