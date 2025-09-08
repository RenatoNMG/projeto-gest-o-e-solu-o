<?php 

require_once __DIR__ ."/../database/database.php";

$id_empresa = $_SESSION['id_empresa'];
$conn = Database::getConnection();

// Adicionar evento
if(isset($_POST['add_event'])) {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];

    $stmt = $conn->prepare("INSERT INTO agenda (id_empresa, titulo, descricao, data, hora) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id_empresa, $titulo, $descricao, $data, $hora]);

    // Redirecionar mantendo mês/ano e forçando a agenda aberta
    
}

// Definir mês e ano (GET ou atual)
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('m');
$ano = isset($_GET['ano']) ? intval($_GET['ano']) : date('Y');

// Pegar eventos do mês
$stmt = $conn->prepare("SELECT * FROM agenda WHERE id_empresa = ? AND MONTH(data) = ? AND YEAR(data) = ?");
$stmt->execute([$id_empresa, $mes, $ano]);
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preparar array JS
$eventos_js = [];
foreach($eventos as $ev){
    $eventos_js[$ev['data']][] = $ev;
}

// Calcular mês anterior e próximo
$mes_anterior = $mes - 1;
$ano_anterior = $ano;
if($mes_anterior < 1){ $mes_anterior = 12; $ano_anterior--; }

$mes_proximo = $mes + 1;
$ano_proximo = $ano;
if($mes_proximo > 12){ $mes_proximo = 1; $ano_proximo++; }
?>

<!-- Conteúdo do módulo Agenda -->
<style>
/* Agenda exclusiva */
.agenda-calendar { 
    display:grid; 
    grid-template-columns: repeat(7, 1fr); 
    gap:5px; 
    margin-top:20px; 
    width:100%; 
    max-width:600px; 
}

.agenda-day { 
    background:#42464b; 
    padding:10px; 
    cursor:pointer; 
    text-align:center; 
    border-radius:5px; 
    min-height:60px; 
    position:relative; 
    color:#f5f5f5;
}

.agenda-day.today { 
    background:#5865f2; 
    color:#fff;
}

.agenda-event { 
    font-size:10px; 
    background:#ffcc00; 
    color:#111; 
    border-radius:3px; 
    margin-top:3px; 
    padding:1px 3px; 
    display:block; 
}

/* Modal exclusivo */
.agenda-modal { 
    display:none; 
    position:fixed; 
    top:0; 
    left:0; 
    width:100%; 
    height:100%; 
    background:rgba(0,0,0,0.7); 
    justify-content:center; 
    align-items:center; 
    z-index:1000;
}

.agenda-modal-content { 
    background:#fff; 
    color:#111; 
    padding:20px; 
    border-radius:5px; 
    width:300px; 
}

.agenda-modal-content label { 
    color:#111; 
    display:block; 
    margin-top:10px; 
    margin-bottom:3px; 
    font-weight:bold;
}

.agenda-modal-content input,
.agenda-modal-content textarea { 
    color:#111; 
    background:#f0f0f0; 
    border:1px solid #ccc; 
    border-radius:4px; 
    padding:5px; 
    width:100%; 
    box-sizing:border-box; 
}

.agenda-close { 
    cursor:pointer; 
    float:right; 
    font-weight:bold; 
}

button.agenda-nav, .agenda-modal-content button { 
    background:#5865f2; 
    color:#fff; 
    border:none; 
    padding:5px 10px; 
    border-radius:4px; 
    cursor:pointer; 
}

button.agenda-nav:hover, .agenda-modal-content button:hover { 
    background:#4449c5; 
}

/* Lista de eventos */
.agenda-event-list { 
    width:100%; 
    max-width:600px; 
    margin-top:20px; 
    background:#42464b; 
    padding:10px; 
    border-radius:5px; 
    color:#f5f5f5; 
}

.agenda-event-list ul { 
    list-style:none; 
    padding:0; 
    margin:0; 
}

.agenda-event-list li { 
    margin-bottom:5px; 
    cursor:pointer; 
    color:#f5f5f5; 
}

.agenda-event-list li:hover { 
    background:#5865f2; 
    border-radius:3px; 
    padding:2px; 
    color:#fff; 
}
</style>

<div>
    <h3>Agenda - <?php echo sprintf("%02d/%d", $mes, $ano); ?></h3>

    <!-- Navegação de meses -->
    <div>
        <a href="?mes=<?php echo $mes_anterior; ?>&ano=<?php echo $ano_anterior; ?>&openAgenda=1"><button class="agenda-nav">&lt; Mês Anterior</button></a>
        <a href="?mes=<?php echo $mes_proximo; ?>&ano=<?php echo $ano_proximo; ?>&openAgenda=1"><button class="agenda-nav">Próximo Mês &gt;</button></a>
    </div>

    <!-- Lista de eventos -->
    <div class="agenda-event-list">
        <h4>Eventos do Mês</h4>
        <?php if(count($eventos) > 0): ?>
            <ul>
                <?php foreach($eventos as $ev): ?>
                    <li data-date="<?php echo $ev['data']; ?>">
                        <strong><?php echo date('d/m', strtotime($ev['data'])); ?> <?php echo $ev['hora']; ?></strong>: 
                        <?php echo htmlspecialchars($ev['titulo']); ?> - <?php echo htmlspecialchars($ev['descricao']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Nenhum evento neste mês.</p>
        <?php endif; ?>
    </div>

    <div id="calendar" class="agenda-calendar"></div>

    <div class="agenda-modal" id="modal">
        <div class="agenda-modal-content">
            <span class="agenda-close" onclick="closeModal()">&times;</span>
            <h3>Adicionar Evento</h3>
            <form method="POST">
                <input type="hidden" name="data" id="event_date">
                <label>Título:</label>
                <input type="text" name="titulo" required>
                <label>Descrição:</label>
                <textarea name="descricao"></textarea>
                <label>Hora:</label>
                <input type="time" name="hora">
                <br><br>
                <button type="submit" name="add_event">Salvar</button>
            </form>
        </div>
    </div>
</div>

<script>
const calendarEl = document.getElementById('calendar');
const eventos = <?php echo json_encode($eventos_js); ?>;
const ano = <?php echo $ano; ?>;
const mes = <?php echo $mes-1; ?>; // JS usa 0-11

// Criar dias do mês
const diasMes = new Date(ano, mes+1, 0).getDate();
const hoje = new Date();
for(let i=1; i<=diasMes; i++){
    const diaEl = document.createElement('div');
    diaEl.classList.add('agenda-day');

    const dataString = ano+'-'+String(mes+1).padStart(2,'0')+'-'+String(i).padStart(2,'0');

    // Marcar hoje
    if(i === hoje.getDate() && mes === hoje.getMonth() && ano === hoje.getFullYear()){
        diaEl.classList.add('today');
    }

    diaEl.textContent = i;

    // Adicionar eventos
    if(eventos[dataString]){
        eventos[dataString].forEach(ev => {
            const evEl = document.createElement('span');
            evEl.classList.add('agenda-event');
            evEl.textContent = ev.titulo;
            diaEl.appendChild(evEl);
        });
    }

    // Abrir modal
    diaEl.onclick = () => {
        document.getElementById('event_date').value = dataString;
        document.getElementById('modal').style.display = 'flex';
    }

    calendarEl.appendChild(diaEl);
}

// Abrir modal ao clicar na lista de eventos
document.querySelectorAll('.agenda-event-list li').forEach(li => {
    li.onclick = () => {
        const data = li.getAttribute('data-date');
        document.getElementById('event_date').value = data;
        document.getElementById('modal').style.display = 'flex';
    }
});

function closeModal(){
    document.getElementById('modal').style.display = 'none';
}

// Se URL tiver openAgenda=1, garante que agenda abre
window.addEventListener('DOMContentLoaded', () => {
    if (new URLSearchParams(window.location.search).has('openAgenda')) {
        document.getElementById('calendar').style.display = 'grid';
    }
});
</script>
