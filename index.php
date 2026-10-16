<?php
// index.php - Listagem de Reservas
require_once 'conexao.php';

// Processar exclusão se solicitada
$mensagem = "";
if (isset($_GET['excluir_id'])) {
    $id_excluir = filter_input(INPUT_GET, 'excluir_id', FILTER_VALIDATE_INT);
    if ($id_excluir) {
        try {
            $stmtExcluir = $pdo->prepare("DELETE FROM tb_reserva WHERE id_reserva = :id");
            $stmtExcluir->execute([':id' => $id_excluir]);
            $mensagem = "<div class='alerta sucesso'>Reserva excluída com sucesso!</div>";
        } catch (PDOException $e) {
            $mensagem = "<div class='alerta erro'>Erro ao excluir reserva: " . $e->getMessage() . "</div>";
        }
    }
}

// Filtro de Busca
$busca = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_SPECIAL_CHARS);

// Consulta SQL com INNER JOIN
$sql = "SELECT r.id_reserva, h.nome AS nome_hospede, q.numero_quarto, q.tipo AS tipo_quarto, 
                r.data_checkin, r.data_checkout
        FROM tb_reserva r
        INNER JOIN tb_hospede h ON r.id_hospede = h.id_hospede
        INNER JOIN tb_quarto q ON r.id_quarto = q.id_quarto";

if ($busca) {
    $sql .= " WHERE h.nome LIKE :busca";
}
$sql .= " ORDER BY r.data_checkin DESC";

$stmt = $pdo->prepare($sql);
if ($busca) {
    $stmt->bindValue(':busca', "%$busca%");
}
$stmt->execute();
$reservas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Hotel Imperial Stay - Painel de Reservas</title>
    <link rel="stylesheet" href="estilo.css">
</head>

<body>
    <div class="container">
        <h1>Hotel Imperial Stay</h1>
        <p class="subtitulo">Sistema de Gestão de Reservas</p>

        <nav class="menu">
            <a href="index.php" class="btn ativo">Listar Reservas</a>
            <a href="reserva_cadastro.php" class="btn btn-principal">+ Nova Reserva</a>
        </nav>

        <?= $mensagem ?>

        <!-- Form de Pesquisa (Desafio Extra) -->
        <form method="GET" action="index.php" class="form-busca">
            <input type="text" name="busca" placeholder="Buscar por nome do hóspede..."
                value="<?= htmlspecialchars($busca ?? '') ?>">
            <button type="submit" class="btn">Pesquisar</button>
            <?php if ($busca): ?>
                <a href="index.php" class="btn btn-limpar">Limpar Filtro</a>
            <?php endif; ?>
        </form>

        <h2>Listagem de Reservas</h2>

        <?php if (count($reservas) > 0): ?>
            <table class="tabela">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hóspede</th>
                        <th>Quarto</th>
                        <th>Tipo</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $res): ?>
                        <tr>
                            <td><?= $res['id_reserva'] ?></td>
                            <td><?= htmlspecialchars($res['nome_hospede']) ?></td>
                            <td>Quarto <?= htmlspecialchars($res['numero_quarto']) ?></td>
                            <td><?= htmlspecialchars($res['tipo_quarto']) ?></td>
                            <td><?= date('d/m/Y', strtotime($res['data_checkin'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($res['data_checkout'])) ?></td>
                            <td>
                                <a href="reserva_editar.php?id=<?= $res['id_reserva'] ?>" class="btn-acao editar">Editar</a>
                                <a href="index.php?excluir_id=<?= $res['id_reserva'] ?>" class="btn-acao excluir"
                                    onclick="return confirm('Tem certeza que deseja excluir esta reserva?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="registro-vazio">Nenhuma reserva encontrada.</p>
        <?php endif; ?>
    </div>
</body>

</html>