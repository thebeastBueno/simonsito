<?php
// Habilitar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de la base de datos (asegúrate de que coincidan con tu api.php)
$servername = "localhost";
$username_db = "root"; // Tu usuario de MySQL
$password_db = "";     // Tu contraseña de MySQL
$dbname = "simonsito_db";

// Crear conexión
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

echo "Conexión a la base de datos exitosa.<br>";

// Datos de prueba para el login (DEBEN COINCIDIR CON LOS QUE INTENTAS USAR EN EL FRONTEND)
$test_username = 'admin'; // El nombre de usuario que insertaste
$test_password = '123456'; // LA CONTRASEÑA ORIGINAL, NO EL HASH
$test_role = 'administrador'; // El rol que insertaste

// Consulta para obtener el usuario
$stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? AND role = ?");
if (!$stmt) {
    die("Error al preparar la consulta: " . $conn->error);
}
$stmt->bind_param("ss", $test_username, $test_role);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "Usuario encontrado en la base de datos: " . $user['username'] . " con rol: " . $user['role'] . "<br>";

    // Verificar la contraseña
    if (password_verify($test_password, $user['password'])) {
        echo "¡Contraseña verificada correctamente! Login exitoso.<br>";
    } else {
        echo "ERROR: Contraseña no coincide con el hash almacenado.<br>";
        echo "Hash almacenado: " . $user['password'] . "<br>";
        echo "Contraseña intentada: " . $test_password . "<br>";
    }
} else {
    echo "ERROR: Usuario o rol no encontrado en la base de datos.<br>";
    echo "Intentando buscar username: " . $test_username . " y role: " . $test_role . "<br>";
}

$stmt->close();
$conn->close();
?>
