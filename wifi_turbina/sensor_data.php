<?php
require 'db.php';

$stmt = $pdo->query("SELECT TIMESTAMPDIFF(SECOND, ultima_actualizacion, NOW()) as segundos FROM sensor_estado WHERE id = 1");
$estado = $stmt->fetch(PDO::FETCH_ASSOC);
$sensor_estado = ($estado['segundos'] <= 20) ? "Conectado" : "Desconectado";

$stmt = $pdo->query("SELECT valor FROM voltajes ORDER BY fecha DESC LIMIT 1");
$dato_actual = $stmt->fetch(PDO::FETCH_ASSOC);
$valor_actual = ($sensor_estado === "Conectado") ? ($dato_actual ? $dato_actual['valor'] : "Sin datos") : "Sensor Desconectado";

$stmt = $pdo->query("SELECT valor, fecha FROM voltajes WHERE valor > 1 ORDER BY valor DESC LIMIT 5");
$mejores_valores = $stmt->fetchAll(PDO::FETCH_ASSOC);


echo json_encode(["sensor" => $sensor_estado, "valor" => $valor_actual, "mejores" => $mejores_valores]);
?>
