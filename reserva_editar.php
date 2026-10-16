<?php
// reserva_editar.php - Edição de Reservas Existentes
require_once 'conexao.php';

$mensagem = "";

// 1. Validar e capturar o ID enviado via GET
$id_reserva = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_reserva) {
    header("Location: index.php");
    exit;
}

// 2. Buscar hóspedes para preencher o <select>
$stmtHospedes = $pdo->query("SELECT id_hospede, nome, cpf FROM tb_hospede ORDER BY nome ASC");
$hospedes = $stmtHospedes->fetchAll();

// 3. Buscar quartos para preencher o <select>
$stmtQuartos = $pdo->query("SELECT id_quarto, numero_quarto, tipo, valor_diaria FROM tb_quarto ORDER BY numero_quarto ASC");
$quartos = $stmtQuartos->fetchAll();

// 4. Processar a atualização via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_hospede = filter_input(INPUT_POST, 'id_hospede', FILTER_VALIDATE_INT);
    $id_quarto = filter_input(INPUT_POST, 'id_quarto', FILTER_VALIDATE_INT);
    $data_checkin = filter_input(INPUT_POST, 'data_checkin');
    $data_checkout = filter_input(INPUT_POST, 'data_checkout');

    // Validação de Campos Obrigatórios
    if (!$id_hospede || !$id_quarto || empty($data_checkin) || empty($data_checkout)) {
        $mensagem = "<div class='alerta erro'>Preencha todos os campos obrigatórios!</div>";
    } 
    // Validação de Regra de Negócio: Check-out posterior ao Check-in
    elseif ($data_checkout <= $data_checkin) {
        $mensagem = "<div class='alerta erro'>A data de check-out deve ser posterior à data de check-in!</div>";
    } 
    else {
        try {
            $sql = "UPDATE tb_reserva 
                    SET id_hospede = :id_hospede, 
                        id_quarto = :id_quarto, 
                        data_checkin = :data_checkin, 
                        data_checkout = :data_checkout 
                    WHERE id_reserva = :id_reserva";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_hospede'   => $id_hospede,
                ':id_quarto'    => $id_quarto,
                ':data_checkin'  => $data_checkin,
                ':data_checkout' => $data_checkout,
                ':id_reserva'   => $id_reserva
            ]);

            $mensagem = "<div class='alerta sucesso'>Reserva atualizada com sucesso! <a href='index.php'>Ver listagem</a></div>";
        } catch (PDOException $e) {
            $mensagem = "<div class='alerta erro'>Erro ao atualizar reserva: " . $e->getMessage() . "</div>";
        }
    }
}

// 5. Buscar os dados atuais do registro selecionado para preencher o formulário
$stmtReserva = $pdo->prepare("SELECT * FROM tb_reserva WHERE id_reserva = :id");
$stmtReserva->execute([':id' => $id_reserva]);
$reservaAtual = $stmtReserva->fetch();

if (!$reservaAtual) {
    die("<div class='container'><p class='alerta erro'>Registro de reserva não encontrado!</p><a href='index.php' class='btn'>Voltar</a></div>");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Reserva - Hotel Imperial Stay</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="container">
        <h1>Hotel Imperial Stay</h1>
        <p class="subtitulo">Editar Reserva #<?= $reservaAtual['id_reserva'] ?></p>

        <nav class="menu">
            <a href="index.php" class="btn">Voltar para Listagem</a>
        </nav>

        <?= $mensagem ?>

        <form method="POST" action="reserva_editar.php?id=<?= $id_reserva ?>" class="formulario">
            <div class="campo">
                <label for="id_hospede">Hóspede:</label>
                <select name="id_hospede" id="id_hospede" required>
                    <option value="">-- Selecione o Hóspede --</option>
                    <?php foreach ($hospedes as $h): ?>
                        <option value="<?= $h['id_hospede'] ?>" <?= ($reservaAtual['id_hospede'] == $h['id_hospede']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($h['nome']) ?> (CPF: <?= htmlspecialchars($h['cpf']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="id_quarto">Quarto:</label>
                <select name="id_quarto" id="id_quarto" required>
                    <option value="">-- Selecione o Quarto --</option>
                    <?php foreach ($quartos as $q): ?>
                        <option value="<?= $q['id_quarto'] ?>" <?= ($reservaAtual['id_quarto'] == $q['id_quarto']) ? 'selected' : '' ?>>
                            Quarto <?= $q['numero_quarto'] ?> - <?= htmlspecialchars($q['tipo']) ?> (R$ <?= number_format($q['valor_diaria'], 2, ',', '.') ?>/dia)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="data_checkin">Data de Check-in:</label>
                <input type="date" name="data_checkin" id="data_checkin" value="<?= $reservaAtual['data_checkin'] ?>" required>
            </div>

            <div class="campo">
                <label for="data_checkout">Data de Check-out:</label>
                <input type="date" name="data_checkout" id="data_checkout" value="<?= $reservaAtual['data_checkout'] ?>" required>
            </div>

            <button type="submit" class="btn btn-principal">Atualizar Reserva</button>
        </form>
    </div>
</body>
</html>