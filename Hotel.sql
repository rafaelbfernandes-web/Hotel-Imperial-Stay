-- 1. CRIAR E SELECIONAR O BANCO DE DADOS
CREATE DATABASE IF NOT EXISTS db_hotel;
USE db_hotel;

-- 2. CRIAÇÃO DAS TABELAS (Respeitando as dependências das chaves estrangeiras)

-- Tabela 1: tb_hospede (Tabela pai)
CREATE TABLE tb_hospede (
    id_hospede INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(15) NOT NULL
);

-- Tabela 2: tb_quarto (Tabela pai)
CREATE TABLE tb_quarto (
    id_quarto INT AUTO_INCREMENT PRIMARY KEY,
    numero_quarto INT NOT NULL UNIQUE,
    tipo VARCHAR(30) NOT NULL,
    capacidade INT NOT NULL,
    valor_diaria DECIMAL(10, 2) NOT NULL
);

-- Tabela 3: tb_reserva (Tabela filho - depende de tb_hospede e tb_quarto)
CREATE TABLE tb_reserva (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_hospede INT NOT NULL,
    id_quarto INT NOT NULL,
    data_checkin DATE NOT NULL,
    data_checkout DATE NOT NULL,
    CONSTRAINT fk_reserva_hospede FOREIGN KEY (id_hospede) REFERENCES tb_hospede(id_hospede) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_reserva_quarto FOREIGN KEY (id_quarto) REFERENCES tb_quarto(id_quarto) ON DELETE CASCADE ON UPDATE CASCADE
);

-- 3. INSERÇÃO DE REGISTROS (5 registros por tabela)

-- Inserindo registros em tb_hospede
INSERT INTO tb_hospede (nome, cpf, email, telefone) VALUES
('Ana Clara Silva', '111.222.333-01', 'ana.silva@email.com', '(19) 99111-2233'),
('Bruno Oliveira', '222.333.444-02', 'bruno.o@email.com', '(19) 99222-3344'),
('Carla Santos', '333.444.555-03', 'carla.s@email.com', '(19) 99333-4455'),
('Diego Souza', '444.555.666-04', 'diego.souza@email.com', '(19) 99444-5566'),
('Elena Ferreira', '555.666.777-05', 'elena.f@email.com', '(19) 99555-6677');

-- Inserindo registros em tb_quarto
INSERT INTO tb_quarto (numero_quarto, tipo, capacidade, valor_diaria) VALUES
(101, 'Standard', 2, 180.00),
(102, 'Standard', 2, 180.00),
(201, 'Luxo', 3, 320.00),
(202, 'Luxo', 4, 400.00),
(301, 'Suíte Presidencial', 2, 850.00);

-- Inserindo registros em tb_reserva
INSERT INTO tb_reserva (id_hospede, id_quarto, data_checkin, data_checkout) VALUES
(1, 3, '2026-11-01', '2026-11-05'),
(2, 1, '2026-11-02', '2026-11-04'),
(3, 5, '2026-11-10', '2026-11-15'),
(4, 2, '2026-11-12', '2026-11-14'),
(5, 4, '2026-11-20', '2026-11-25');

-- 4. CONSULTAS DE VERIFICAÇÃO (SELECT)

SELECT * FROM tb_hospede;
SELECT * FROM tb_quarto;
SELECT * FROM tb_reserva;