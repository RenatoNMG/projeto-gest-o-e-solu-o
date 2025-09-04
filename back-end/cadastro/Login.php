<?php
// A sessão DEVE ser a primeira coisa no script.
session_start();

// Bloco de processamento do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if ($email && $senha) {
        require_once __DIR__ . '/../database/database.php';
        require_once __DIR__ . '/../model/Empresa.php';
        require_once __DIR__ . '/../dao/EmpresaDAO.php';

        $empresaDAO = new EmpresaDAO();
        $empresa = $empresaDAO->buscarEmpresaPorEmail($email);

        if ($empresa && password_verify($senha, $empresa->getSenha())) {
            // Sucesso no login
            $_SESSION['token'] = $empresa->getToken();
            $_SESSION['id_empresa'] = $empresa->getIdEmpresa();
            header('Location: ../home.php');
            exit();
        } else {
            // Erro de credenciais
            $_SESSION['error_message'] = "Email ou senha inválidos.";
            header('Location: Login.php');
            exit();
        }
    } else {
        // Erro de campos vazios
        $_SESSION['error_message'] = "Todos os campos são obrigatórios.";
        header('Location: Login.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../css/login.css">
</head>
<body>

    <div class="login-container">
        <form action="Login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
            
            <button type="submit">Entrar</button>
        </form>

        <?php
            // Bloco PHP para exibir a mensagem de erro (agora abaixo do form)
            if (isset($_SESSION['error_message'])) {
                echo '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']); // Limpa a mensagem para não mostrar de novo
            }
        ?>
        
        <a href="Cadastro.php">Cadastrar Empresa</a>
        <a href="projeto-gest-o-e-solu-o/index.html">Voltar</a>
    </div>

</body>
</html>