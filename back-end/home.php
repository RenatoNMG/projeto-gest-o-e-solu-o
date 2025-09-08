<?php
session_start();
if (!isset($_SESSION['token'])) {
    header('Location: cadastro/Cadastro.php');
    exit();
}

require_once __DIR__ . '/dao/CampoDAO.php';
require_once __DIR__ . '/model/Campo.php';
require_once __DIR__ . '/dao/ModuloDAO.php';
require_once __DIR__ . '/model/Modulo.php';
require_once __DIR__ . '/dao/CardDAO.php';
require_once __DIR__ . '/model/Card.php';
require_once __DIR__ . '/dao/DadosDAO.php';
require_once __DIR__ . '/model/Dados.php';
require_once __DIR__ . '/dao/EmpresaDAO.php';
require_once __DIR__ . '/model/Empresa.php';
require_once __DIR__ . '/dao/ImagemController.php';
require_once __DIR__ . '/model/Logo.php';
require_once __DIR__ . '/dao/SubModuloDAO.php';
require_once __DIR__ . '/model/SubModulo.php';
require_once __DIR__ . '/dao/ValorDAO.php';
require_once __DIR__ . '/model/Valor.php';
require_once __DIR__ . '/acoes/Pesquisa.php'; // adicionei sua classe de pesquisa

$submoduloDAO = new SubmoduloDAO();
$valorDAO = new ValorDAO();
$camposDAO = new CampoDAO();
$dadosDAO = new DadosDAO();
$cardsDAO = new CardDAO();
$moduloDAO = new ModuloDAO();
$empresaDAO = new EmpresaDAO();
$logoController = new ImagemController();
$pesquisa = new Pesquisa();

$logo = $logoController->getImagemPorEmpresa($_SESSION['id_empresa']);
$campos = $camposDAO->listarCamposPorEmpresa($_SESSION['id_empresa']);
$empresa = $empresaDAO->buscarEmpresaPorId($_SESSION['id_empresa']);

$modulos = [];
$modulo = [];
$submodulosComValores = [];
$resultadosPesquisa = [];

// pesquisa via GET
if (isset($_GET['pesquisa'])) {
    $termo = trim($_GET['pesquisa']);
    $resultadosPesquisa = $pesquisa->buscarTodos($_SESSION['id_empresa'], $termo);
}

// módulos e submódulos
if (isset($_GET['id'])) {
    $id_campo = $_GET['id'];
    $modulos = $moduloDAO->listarModulosPorCampo($id_campo, $_SESSION['id_empresa']);
}

if (isset($_GET['id_modulo'])) {
    $modulo = $moduloDAO->getById($_GET['id_modulo']);
    $submodulos = $submoduloDAO->getPorIdModulo($_GET['id_modulo']);

    foreach ($submodulos as $submodulo) {
        $valores = $valorDAO->getBySubModulos($submodulo->getId());
        $somaValores = 0;
        $textoValores = [];
        foreach ($valores as $valor) {
            if (is_numeric($valor->getValor())) $somaValores += $valor->getValor();
            else $textoValores[] = $valor->getValor();
        }
        $submodulosComValores[] = [
            'nome' => $submodulo->getNome(),
            'valor' => $somaValores,
            'texto' => implode(" | ", $textoValores)
        ];
    }

    // gráfico
    $submodulosGrafico = $submoduloDAO->getSubmodulosComItens($_GET['id_modulo']);
    $graficoValores = [];
    foreach ($submodulosGrafico as $submodulo) {
        $nome = $submodulo->getNomeSubmodulo();
        $valor = $submodulo->getNomeItem();
        if (is_numeric($valor)) {
            if (!isset($graficoValores[$nome])) $graficoValores[$nome] = 0;
            $graficoValores[$nome] += $valor;
        }
    }
    $labels = array_keys($graficoValores);
    $data = array_values($graficoValores);
}

// logo fallback
$logoPath = ($logo && file_exists($logo->getCaminho())) ? $logo->getCaminho()
    : "https://static.vecteezy.com/ti/vetor-gratis/p1/5538023-forma-simples-montanha-preto-branco-circulo-logo-simbolo-icone-design-grafico-ilustracao-ideia-criativo-vetor.jpg";
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão & Solução</title>
    <link rel="stylesheet" href="../css/styles3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <div class="container">
        <header class="header">
            <div class="buttonelogo">
                <div class="menu-toggle"><i class="fas fa-bars"></i></div>
                <div class="logo">
                    <a href="configuracao.php"><img src="<?= $logoPath ?>" alt="Logo"></a>
                </div>
                <button id="abrirAgendaBtn">Abrir Agenda</button>

            </div>
            <div class="title">
                <h1><?= $empresa ? $empresa->getNome() : "Gestão & Solução" ?></h1>
            </div>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <form method="GET">
                    <input type="text" name="pesquisa" placeholder="Pesquisa" value="<?= isset($termo) ? htmlspecialchars($termo) : '' ?>">
                    <button type="submit">Buscar</button>
                </form>
            </div>
        </header>

        <aside class="sidebar">
            <?php foreach ($campos as $campo): ?>
                <a href="?id=<?= $campo->getIdCampo(); ?>">
                    <nav>
                        <ul>
                            <li style="box-shadow: 3px 3px 1px <?= $campo->getCor(); ?>;border: 1px solid black">
                                <?= $campo->getNome(); ?>
                            </li>
                        </ul>
                    </nav>
                </a>
            <?php endforeach; ?>
            <div class="add-button"><a href="acoes/Addcampo.php"><i class="fas fa-plus-circle"></i></a></div>
        </aside>

        <main class="main-content">
            <div id="agendaContainer" style="display:none;">
                <?php include 'agenda/agenda.php'; ?>
            </div>

            <script>
                document.getElementById('abrirAgendaBtn').onclick = function() {
                    const container = document.getElementById('agendaContainer');
                    if (container.style.display === 'none' || container.style.display === '') {
                        container.style.display = 'block';
                    } else {
                        container.style.display = 'none';
                    }
                }

                window.addEventListener('DOMContentLoaded', () => {
                    const container = document.getElementById('agendaContainer');
                    const urlParams = new URLSearchParams(window.location.search);

                    // Abre a agenda se a URL tiver mes+ano ou openAgenda=1
                    if ((urlParams.has('mes') && urlParams.has('ano')) || urlParams.has('openAgenda')) {
                        container.style.display = 'block';
                    }
                });
            </script>



            <ul>
                <?php foreach ($modulos as $moduloItem): ?>
                    <li><a href="?id_modulo=<?= $moduloItem->getId(); ?>"><?= $moduloItem->getNome(); ?></a></li>
                <?php endforeach; ?>
            </ul>

            <div class="cards-table">
                <?php if (isset($_GET['id_modulo'])): ?>
                    <div class="profile-box">
                        <h2 class="profile-title"><?= $modulo->getNome(); ?></h2>
                        <div class="profile-grid">
                            <?php
                            $submodulos = $submoduloDAO->getPorIdModulo($_GET['id_modulo']);
                            foreach ($submodulos as $submodulo):
                                $valores = $valorDAO->getBySubModulos($submodulo->getId());
                            ?>
                                <div class="profile-group">
                                    <label><?= $submodulo->getNome(); ?></label>
                                    <input type="text" value="<?php
                                                                $valoresText = [];
                                                                foreach ($valores as $valor) $valoresText[] = $valor->getValor();
                                                                echo implode(" | ", $valoresText);
                                                                ?>" readonly>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="acoes/Adicionarsubmodulo.php?id_modulo=<?= $_GET['id_modulo']; ?>">
                            <button class="profile-edit-button">Editar</button>
                        </a>
                    </div>

                    <div class="chart-container">
                        <h2 class="chart-title">Gráfico</h2>
                        <canvas id="myChart"></canvas>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($resultadosPesquisa)): ?>
                <div class="modal fade show" style="display:block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content text-dark">
                            <div class="modal-header">
                                <h5 class="modal-title">Resultados da Pesquisa</h5>
                                <a href="home.php" class="btn-close"></a>
                            </div>
                            <div class="modal-body">
                                <ul>
                                    <?php foreach ($resultadosPesquisa as $res): ?>
                                        <li>
                                            <a href="?id_modulo=<?= $res['id_modulo'] ?>">
                                                <?= htmlspecialchars($res['nome']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php if (isset($_GET['id_modulo'])): ?>
        <script>
            const ctx = document.getElementById('myChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($labels) ?>,
                    datasets: [{
                        label: 'Média',
                        data: <?= json_encode($data) ?>,
                        backgroundColor: 'rgba(0, 13, 131, 0.78)',
                        borderColor: 'rgb(214, 18, 0)',
                        borderWidth: 2,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true
                }
            });
        </script>
    <?php endif; ?>

    <!-- Script do Toggle da Sidebar -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.querySelector('.menu-toggle');
            const sidebar = document.querySelector('.sidebar');

            // Função para alternar sidebar
            const toggleSidebar = () => {
                if (window.innerWidth <= 1024) {
                    sidebar.classList.toggle('open'); // mobile/tablet
                } else {
                    sidebar.classList.toggle('closed'); // desktop
                }
            };

            toggleBtn.addEventListener('click', toggleSidebar);

            // Ajusta sidebar ao redimensionar
            const ajustarSidebar = () => {
                if (window.innerWidth > 1024) {
                    sidebar.classList.remove('open');
                } else {
                    sidebar.classList.remove('closed');
                }
            };

            window.addEventListener('resize', ajustarSidebar);

            // Inicializa sidebar corretamente ao carregar
            ajustarSidebar();
        });
    </script>

</body>

</html>