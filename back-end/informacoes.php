<?php
// 1. Inclui o arquivo do modelo para ter acesso aos dados
require_once 'GestaoModel.php'; 
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['modalTitle']); ?></title>
    <link rel="stylesheet" href="css/informacoes.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/akar-icons-fonts"></script>
</head>

<body>
    <div class="modal-backdrop">
        <div class="modal-container">
            <header class="modal-header">
                <h1 class="modal-title"><?php echo htmlspecialchars($data['modalTitle']); ?></h1>
                <a href="configuracao.php"><i class="ai-cross"></i></a>
            </header>
            <div class="modal-body">
                <article class="content">
                    <h2 class="content-title"><?php echo htmlspecialchars($data['contentTitle']); ?></h2>
                    
                    <?php foreach ($data['paragraphs'] as $paragraph): ?>
                        <p><?php echo htmlspecialchars($paragraph); ?></p>
                    <?php endforeach; ?>

                </article>
            </div>
        </div>
    </div>
</body>

</html>