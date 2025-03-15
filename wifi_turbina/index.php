<?php
require 'db.php';

$stmt = $pdo->query("SELECT TIMESTAMPDIFF(SECOND, ultima_actualizacion, NOW()) as segundos FROM sensor_estado WHERE id = 1");
$estado = $stmt->fetch(PDO::FETCH_ASSOC);
$sensor_estado = ($estado['segundos'] <= 20) ? "Conectado" : "Desconectado";

$stmt = $pdo->query("SELECT valor FROM voltajes ORDER BY fecha DESC LIMIT 1");
$dato_actual = $stmt->fetch(PDO::FETCH_ASSOC);
$valor_actual = ($sensor_estado === "Conectado") ? ($dato_actual ? $dato_actual['valor'] : "Sin datos") : "Sensor Desconectado";

$stmt = $pdo->query("SELECT valor, fecha FROM voltajes ORDER BY valor DESC LIMIT 5");
$mejores_valores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado del Sensor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
    <script>
        function actualizarValor() {
            fetch('sensor_data.php')
                .then(response => response.json())
                .then(data => {
                    let valorElemento = document.getElementById('valorActual');
                    
                    if (data.sensor === "Desconectado") {
                        valorElemento.innerText = "Sensor Desconectado";
                        valorElemento.classList.add("text-red-500", "animate-pulse");
                        valorElemento.classList.remove("text-black");
                    } else {
                        valorElemento.innerText = data.valor + " V";
                        valorElemento.classList.add("text-black");
                        valorElemento.classList.remove("text-red-500", "animate-pulse");
                    }

                    let mejoresHTML = "";
                    data.mejores.forEach(item => {
                        mejoresHTML += `<li class='list-group-item border-l-4 border-blue-500 bg-gray-50 p-2 rounded-lg shadow-md'>Voltage registrado: ${item.valor} V - Fecha: ${item.fecha}</li>`;
                    });
                    document.getElementById('mejoresValores').innerHTML = mejoresHTML;
                });
        }
        setInterval(actualizarValor, 2000);
    </script>
    <script>
        

        function verificarConexion() {
            fetch('sensor_estado.php')
                .then(response => response.json())
                .then(data => {
                    if (data.estado === "Desconectado") {
                        location.reload(); // Recargar la página si el sensor está desconectado
                    }
                });
        }

        
        setInterval(verificarConexion, 5000); // Verifica cada 5 segundos
    </script>
</head>
<body class="bg-gradient-to-r from-blue-400 to-indigo-500 py-5 text-white">
    <div class="container text-center">
        <h1 class="text-4xl font-extrabold mb-4 shadow-lg p-3 rounded-lg bg-white text-blue-600">Estado del Sensor</h1>
        <p class="text-lg bg-white p-3 rounded-lg text-gray-800 shadow-md inline-block">Valor actual del sensor: 
            <strong id="valorActual" class="text-xl text-blue-500">Cargando...</strong>
        </p>
        
        <div class="mt-5 bg-white p-5 rounded-lg shadow-lg max-w-lg mx-auto">
            <h2 class="text-2xl font-semibold text-gray-700 mb-3">Top 5 Valores Históricos</h2>
            <ul id="mejoresValores" class="list-group mt-3 shadow-lg rounded-lg overflow-hidden">
                <?php foreach ($mejores_valores as $valor) { echo "<li class='list-group-item border-l-4 border-green-500 bg-gray-50 p-2 rounded-lg shadow-md'>${valor['valor']} V - ${valor['fecha']}</li>"; } ?>
            </ul>
        </div>

        <div class="mt-5 bg-white p-6 shadow-2xl rounded-lg max-w-md mx-auto border-t-4 border-red-500">
            <h2 class="text-xl font-semibold text-gray-700 mb-3">Borrar registros</h2>
            <form method="POST" action="delete.php" class="space-y-3">
                <label for="password" class="block text-gray-600 font-medium">Contraseña:</label>
                <input type="password" name="password" required class="form-control border-gray-300 focus:ring focus:ring-blue-300">
                <button type="submit" class="btn btn-danger w-full hover:bg-red-700 transition-all duration-300">Borrar datos</button>
            </form>
        </div>
    </div>

    <script>actualizarValor();</script>
</body>
</html>
