<?php

session_start();


if (!isset($_SESSION['token'])) {
    header('Location: cadastro/Cadastro.php'); // Redirecionar para a página de cadastro se não estiver autenticado
    exit();
    echo "Você não está autenticado. Por favor, faça o cadastro.";
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






$camposDAO = new CampoDAO();
$dadosDAO = new DadosDAO();
$cardsDAO = new CardDAO();
$moduloDAO = new ModuloDAO();
$empresaDAO = new EmpresaDAO();
$logoController = new ImagemController();


$logo = $logoController->getImagemPorEmpresa($_SESSION['id_empresa']);
$campos = $camposDAO->listarCamposPorEmpresa($_SESSION['id_empresa']);
$empresa = $empresaDAO->buscarEmpresaPorId($_SESSION['id_empresa']);



$cards = [];
$modulos = [];
$dados = [];
$modulo = [];


if (isset($_GET['id'])) {
    $id_campo = $_GET['id'];
    $modulos = $moduloDAO->listarModulosPorCampo($id_campo, $_SESSION['id_empresa']);
}
if (isset($_GET['id_modulo'])){
    $modulo = $moduloDAO->getById($_GET['id_modulo']);
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
    <title>Gestão & Solução</title>
    <link rel="stylesheet" href="../css/styles3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/graficos.css">

</head>

<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <a href="configuracao.php">
                    <img src="<?= $logoPath ?>" alt="Logo">
                </a>
                <div class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
            <div class="title">
                <h1> <?= $empresa ? $empresa->getNome() : "Gestão & Solução" ?></h1>
            </div>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Pesquisa">
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
            <div class="add-button">
                <a href="acoes/Addcampo.php"><i class="fas fa-plus-circle"></i></a>
            </div>
        </aside>


        <main class="main-content">

            <!-- modulos -->
            <ul>
                <?php foreach ($modulos as $modulo): ?>
                    <li><a href="?id_modulo=<?= $modulo->getId(); ?>"><?= $modulo->getNome(); ?></a></li>
                <?php endforeach; ?>
            </ul>

            <!-- cards -->
            <div class="cards-table">
                <?php if(isset($_GET['id_modulo'])): ?>
                    <div class="profile-box">
                        <h2 class="profile-title"><?= $modulo->getNome(); ?></h2>
                        <div class="profile-grid">
                            <div class="profile-group">
                                <label for="nome">Nome</label>
                                <input type="text" id="nome" value="Herbert" readonly>
                            </div>
                            <div class="profile-group">
                                <label for="funcao">Função</label>
                                <input type="text" id="funcao" value="Entregador" readonly>
                            </div>
                            <div class="profile-group">
                                <label for="email">Gmail</label>
                                <input type="email" id="email" value="Herbert@gamil.com" readonly>
                            </div>
                            <div class="profile-group">
                                <label for="salario">Salário</label>
                                <input type="text" id="salario" value="R$: 2.000,00" readonly>
                            </div>
                            <div class="profile-group">
                                <label for="contato">Contato</label>
                                <input type="tel" id="contato" value="11 1234 1234" readonly>
                            </div>
                        </div>
                        <a href="acoes/Adicionarsubmodulo.php?id_modulo=<?= $_GET['id_modulo']; ?>"><button class="profile-edit-button">Editar</button></a>
                        <a href="../back-end/home.php"><button type="button">Voltar</button></a>
                    </div>
                    <div class="chart-container">
                        <h2 class="chart-title">Gráfico de Vendas</h2>
                        <canvas id="myChart"></canvas>
                    </div>
                <?php endif; ?>
            </div>

            <!-- adicionar campo -->

            <?php if (isset($_GET['id'])): ?>
                <a href="acoes/Adicionarmodulo.php?id_campo=<?= $_GET['id']; ?>">Adicionar</a>
            <?php endif; ?>
        

        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.querySelector('.menu-toggle');
            const sidebar = document.querySelector('.sidebar');
            const main = document.querySelector('.main-content');

            if (!toggleBtn || !sidebar || !main) return; // se faltar algo, sai sem erro

            toggleBtn.addEventListener('click', (e) => {
                // alterna a classe que seu CSS utiliza: "closed"
                sidebar.classList.toggle('closed');

                // fallback: ajusta margem do main via inline style caso o selector ~ não funcione
                if (sidebar.classList.contains('closed')) {
                    main.style.marginLeft = '0';
                    toggleBtn.setAttribute('aria-expanded', 'false');
                } else {
                    main.style.marginLeft = ''; // retorna ao valor do CSS (margin-left: 240px)
                    toggleBtn.setAttribute('aria-expanded', 'true');
                }
            });

            // opcional: fecha o sidebar ao clicar fora (útil em mobile)
            document.addEventListener('click', (evt) => {
                if (window.innerWidth <= 768) {
                    const target = evt.target;
                    if (!sidebar.contains(target) && !toggleBtn.contains(target) && !sidebar.classList.contains('closed')) {
                        sidebar.classList.add('closed');
                        main.style.marginLeft = '';
                        toggleBtn.setAttribute('aria-expanded', 'false');
                    }
                }
            });
        });

        // graficos 


        document.addEventListener("DOMContentLoaded", () => {
            const ctx = document.getElementById('myChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio','ugsg','uhuhu','ghughuh','hhi'],
                    datasets: [{
                        label: 'Vendas',
                        data: [50, 19, 3, 5, 2,17,8,54,2,12,45,78],
                        backgroundColor: 'rgba(255, 139, 128, 0.7)',
                        borderColor: 'rgba(255, 139, 128, 1)',
                        borderWidth: 2,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: "#fff"
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: "#fff"
                            },
                            grid: {
                                color: "rgba(255,255,255,0.2)"
                            }
                        },
                        y: {
                            ticks: {
                                color: "#fff"
                            },
                            grid: {
                                color: "rgba(255,255,255,0.2)"
                            }
                        }
                    }
                }
            });
        });
    </script>


</body>

</html>