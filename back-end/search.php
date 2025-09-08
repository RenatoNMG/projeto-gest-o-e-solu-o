<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/acoes/Pesquisa.php';

$id_empresa = $_GET['id_empresa'] ?? $_SESSION['id_empresa'] ?? 1;
$termo = $_GET['q'] ?? '';

$pesquisa = new Pesquisa();
$resultados = $pesquisa->buscarTodos($id_empresa, $termo);

// sempre retornar array com chave "submodulo"
echo json_encode(['submodulo' => $resultados]);
?>
