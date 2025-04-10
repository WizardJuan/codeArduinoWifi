<?php
require 'db.php';

header('Content-Type: application/json');

try {
    // Eliminar datos menores a 1V cada 20 segundos
$pdo->query("DELETE FROM voltajes WHERE valor <= 1 AND fecha < NOW() - INTERVAL 20 SECOND");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!isset($data['username'], $data['password'], $data['voltage'])) {
        throw new Exception("Datos inválidos");
    }

    // Usar variables de entorno en lugar de credenciales en texto plano
    $valid_user = getenv('API_USER') ?: 'admin';
    $valid_pass = getenv('API_PASS') ?: '1234';

    if ($data['username'] !== $valid_user || $data['password'] !== $valid_pass) {
        throw new Exception("Credenciales incorrectas");
    }

    $voltage = floatval($data['voltage']);
    if ($voltage <= 0) {
        echo json_encode(["status" => "ignored", "message" => "Voltaje menor a 1V"]);
        exit;
    }

    // Inserción segura en la base de datos
    $stmt = $pdo->prepare("INSERT INTO voltajes (valor, fecha) VALUES (?, NOW())");
    $stmt->execute([$voltage]);

    // Antes de actualizar, verificar si existe el registro
$stmt = $pdo->query("SELECT COUNT(*) FROM sensor_estado WHERE id = 1");
if ($stmt->fetchColumn() == 0) {
    $pdo->query("INSERT INTO sensor_estado (id, ultima_actualizacion) VALUES (1, NOW())");
} else {
    $pdo->query("UPDATE sensor_estado SET ultima_actualizacion = NOW() WHERE id = 1");
}


    echo json_encode(["status" => "success", "message" => "Dato almacenado"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
