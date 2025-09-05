<?php
require_once __DIR__ . '/../dao/SubModuloDAO.php';
require_once __DIR__ . '/../model/SubModulo.php';
require_once __DIR__ . '/../model/Valor.php';
require_once __DIR__ . '/../dao/ValorDAO.php';

$submodulos = [];
if (isset($_GET['id_modulo'])) {
    $id_modulo = $_GET['id_modulo'];
    $submoduloDAO = new SubmoduloDAO();
    $valorDAO = new ValorDAO();
    $submodulos = $submoduloDAO->getPorIdModulo($id_modulo);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submódulos</title>
    <link rel="stylesheet" href="../../css/login.css">
</head>

<body>
    <div class="container">
        <div class="cards-container">
            <?php foreach ($submodulos as $submodulo): ?>
                <div class="card">
                    <h3 class="card-title"><?= $submodulo->getNome(); ?></h3>
                    <h4 class="card-title">
                        <?php
                        $valores = $valorDAO->getBySubModulos($submodulo->getId());
                        foreach ($valores as $valor) {
                            echo $valor->getValor() . "<br>";
                        }
                        ?>
                    </h4>
                    <a href="Adicionarvalor.php?id_submodulo=<?= $submodulo->getId() ?>&id_modulo=<?= $_GET['id_modulo']?>" class="btn-submit">Adicionar Valor do Campo</a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="action-links">
            <a href="../home.php?id_modulo=<?= $_GET['id_modulo'] ?>" class="btn-link">Voltar</a>
            <a href="Adicionaritensubmodulo.php?id_modulo=<?= $_GET['id_modulo'] ?>" class="btn-link">Adicionar Novo Campo</a>
        </div>
    </div>
</body>

</html>