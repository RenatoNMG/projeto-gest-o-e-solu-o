<?php
//     CREATE TABLE empresa (
//     id_empresa INT AUTO_INCREMENT PRIMARY KEY,
//     nome VARCHAR(100) NOT NULL,
//     email VARCHAR(150) NOT NULL UNIQUE,
//     cnpj VARCHAR(18) NOT NULL UNIQUE,
//     senha VARCHAR(255) NOT NULL
// );

require_once __DIR__ . '/dao/EmpresaDAO.php';
require_once __DIR__ . '/model/Empresa.php';
require_once __DIR__ . '/dao/ImagemController.php';
require_once __DIR__ . '/model/Logo.php';


session_start();

if (!isset($_SESSION['token'])) {
    header('Location: cadastro/Cadastro.php'); // Redirecionar para a página de cadastro se não estiver autenticado
    exit();
    echo "Você não está autenticado. Por favor, faça o cadastro.";
}

$logoController = new ImagemController();
$logo = $logoController->getImagemPorEmpresa($_SESSION['id_empresa']);
$empresaDAO = new EmpresaDAO();
$empresa = $empresaDAO->buscarEmpresaPorId($_SESSION['id_empresa']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nomeEmpresa = $_POST['nomeEmpresa'];
    $gmail = $_POST['gmail'];
    $empresa->setNome($nomeEmpresa);
    $empresa->setEmail($gmail);
    if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
        echo "Email inválido. Por favor, insira um email válido.";
        header('Location: configuracao.php');
        exit();
    }
    if ($empresaDAO->buscarEmpresaPorEmail($gmail) && $gmail !== $empresa->getEmail()) {
        echo "Este email já está em uso. Por favor, escolha outro email.";
        header('Location: configuracao.php');
        exit();
    }




    $empresaDAO->UpdateEmpresa($empresa);
    header('Location: home.php');
    exit();
}

$logoPath = ($logo && file_exists($logo->getCaminho()))
    ? $logo->getCaminho()
    : "https://static.vecteezy.com/ti/vetor-gratis/p1/5538023-forma-simples-montanha-preto-branco-circulo-logo-simbolo-icone-design-grafico-ilustracao-ideia-criativo-vetor.jpg";




?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações</title>
    <link rel="stylesheet" href="../css/configuracao.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://unpkg.com/akar-icons-fonts"></script>
</head>
<html>

<body>
    <div class="container">
        <header class="header">
            <h1>Configurações</h1>
            <div class="menu-icon">
                <a href="informacoes.html"><i class="fa fa-info-circle"></i></a>
            </div>

        </header>

        <main class="main-content">
            <div class="profile-section">
                <div class="profile-info">
                    <div class="logo">
                        <img src="<?= $logoPath ?>" alt="Logo">
                    </div>


                    <div class="profile-text">
                        <span class="profile-name"><?= $empresa->getNome(); ?></span>
                        <span class="profile-email"><?= $empresa->getEmail(); ?></span>
                    </div>
                </div>
                <a href="atualizatLogo.php"><button class="edit-button">
                    <i class="fas fa-pencil-alt"></i> Editar imagem
                </button></a>
            </div>


            <form class="form-section" action="configuracao.php" method="POST">
                <div class="form-group">
                    <label for="nome-empresa">Nome da empresa</label>
                    <input type="text" name="nomeEmpresa" value="<?= $empresa->getNome(); ?>" class="input-field">
                </div>
                <div class="form-group">
                    <label for="gmail">Gmail</label>
                    <input type="text" name="gmail" value="<?= $empresa->getEmail(); ?>" class="input-field">
                </div>

                <button type="submit" class="save-button">Salvar</button>

            </form>

            <a href="acoes/sair.php"><i class="ai-door">Sair</i></a>
            <div class="button-group">

                <a href="home.php"><i class="save-button">Voltar</i></a>
            </div>
    </div>

    <!-- <button class="save-button">
    <i class="fas fa-pencil-alt"></i> Salvar
</button>

        </main>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Freedom. Todos os direitos reservados.</p>
    </footer>

    <script src="../js/configuracao.js"></script> -->
</body>

</html>