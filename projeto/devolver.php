<?php

require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        die("Empréstimo inválido!");
    }

    try {

        $pdo->beginTransaction();

        // Busca o empréstimo
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

        // Verifica se já foi devolvido
        if (strtolower($emprestimo['status']) != 'pendente') {
            throw new Exception("Este empréstimo já foi devolvido!");
        }

        // Atualiza o status do empréstimo
        $sql = "UPDATE emprestimos
                SET status = 'devolvido'
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        // Devolve o material ao estoque
        $sql = "UPDATE materiais
                SET quantidade = quantidade + 1
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$emprestimo['material_id']]);

        if ($stmt->rowCount() == 0) {
            throw new Exception("Material não encontrado!");
        }

        $pdo->commit();

        header("Location: emprestimos.php?sucesso=devolvido");
        exit;

    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        die("Erro ao devolver material: " .
            htmlspecialchars($e->getMessage()));
    }
}

header("Location: emprestimos.php");
exit;

?>