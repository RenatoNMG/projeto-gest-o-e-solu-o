<?php
require_once __DIR__ . '/../database/database.php'; // ajuste o caminho

class Pesquisa {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function buscarTodos($id_empresa, $termo = '') {
        try {
            $sql = "SELECT s.* 
                    FROM submodulo s
                    JOIN modulo m ON s.id_modulo = m.id
                    WHERE m.id_empresa = :id_empresa";

            $params = ['id_empresa' => $id_empresa];

            if (!empty($termo)) {
                $sql .= " AND s.nome LIKE :termo";
                $params['termo'] = "%$termo%";
            }

            $sql .= " ORDER BY s.id ASC LIMIT 5";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return ['erro' => $e->getMessage()];
        }
    }
}
?>
