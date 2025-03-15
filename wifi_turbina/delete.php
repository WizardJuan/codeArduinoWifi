<?php
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if ($password !== 'solobucaros') {
        die("Contraseña incorrecta");
    }
    $pdo->query("DELETE FROM voltajes");
    echo '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Datos Eliminados</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <script src="https://cdn.tailwindcss.com"></script>
            <link rel="stylesheet" href="styles.css">
        </head>
        <body class="bg-gradient-to-r from-blue-400 to-indigo-500 h-screen flex flex-col justify-center items-center text-white">
            <div class="bg-white text-gray-800 p-6 rounded-lg shadow-lg max-w-md text-center">
                <h1 class="text-3xl font-bold text-red-500 mb-4">¡Datos Eliminados!</h1>
                <p class="text-lg mb-4">Los registros han sido eliminados correctamente.</p>
                <a href="index.php" class="inline-block">
                    <button class="btn btn-primary text-lg px-6 py-2 rounded-lg shadow-md hover:bg-blue-700 transition-all duration-300">Volver al inicio</button>
                </a>
            </div>
        </body>
        </html>
    ';
}
?>
