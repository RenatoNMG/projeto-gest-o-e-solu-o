<!-- -- Tabela campo
CREATE TABLE campo (
    id_campo INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    nivel INT NOT NULL,
    cor VARCHAR(7), -- Ex: #FFFFFF
    id_empresa INT NOT NULL,
    FOREIGN KEY (id_empresa) REFERENCES empresa(id_empresa) ON DELETE CASCADE
); -->



<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conexão com o banco de dados
   $nome = $_POST['nome'];
   $descricao = $_POST['descricao'];
   $nivel = $_POST['nivel'];
   $cor = $_POST['cor'];

    $id_empresa = $_SESSION['id_empresa'];
    
     require_once __DIR__ . '/../database/database.php';
     require_once __DIR__ . '/../model/Campo.php';
     require_once __DIR__ . '/../dao/CampoDAO.php';
    
     $campo = new Campo(null, $nome, $descricao, $nivel, $cor, $id_empresa);
     $campoDAO = new CampoDAO();
    
     if ($campoDAO->cadastrarCampo($campo)) {
          header('Location: ../home.php'); // Redirecionar para a página inicial após o cadastro
          exit();
     } else {
          echo "Erro ao cadastrar campo.";
          exit();
     }
    }



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../css/login.css">
</head>
<body>
    <form action="Addcampo.php" method="POST">
        <h1>Adicionar Campo</h1>
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" maxlength="20" required>

        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao"></textarea>

        <label for="nivel">Nível:</label>
        <select name="nivel" id="nivel">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
        </select>

        <label for="cor">Cor:</label>
        <input type="color" id="cor" name="cor">

        <input type="hidden" name="id_empresa" value="<?= $_SESSION['id_empresa']; ?>">

        <button type="submit">Cadastrar Campo</button>
        <a href="../home.php"><button type="button">Voltar</button></a>

    </form>

    
</body>
</html>