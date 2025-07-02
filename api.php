<?php
// Habilitar la visualización de errores para depuración (QUITAR EN PRODUCCIÓN)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar la sesión PHP
session_start();

// Asegurarse de que la cabecera JSON se envíe primero para evitar el error de token inesperado
header('Content-Type: application/json');

// --- DEFINICIÓN GLOBAL DE LA RUTA DE MYSQLDUMP ---
// IMPORTANTE: DEBES REEMPLAZAR ESTA LÍNEA CON LA RUTA CORRECTA EN TU SISTEMA.
// Ejemplos:
// Windows (XAMPP): define('MY_SQLDUMP_PATH', '"C:\\xampp\\mysql\\bin\\mysqldump.exe"');
// Linux/macOS:     define('MY_SQLDUMP_PATH', '/usr/bin/mysqldump');
define('MY_SQLDUMP_PATH', '"C:\\xampp\\mysql\\bin\\mysqldump.exe"'); // <<-- ¡MODIFICA ESTA LÍNEA SI ES NECESARIO!


// Configuración de la base de datos
$servername = "localhost";
$username = "root"; // Tu usuario de MySQL
$password = "";     // Tu contraseña de MySQL
$dbname = "simonsito_db";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    // Loguear el error de conexión a la base de datos
    error_log('DB Connection Error: ' . $conn->connect_error);
    echo json_encode(['error' => 'Error de conexión a la base de datos: ' . $conn->connect_error]);
    exit();
}

// Usar isset() y el operador ternario para compatibilidad con PHP < 7.0
$entity = isset($_GET['entity']) ? $_GET['entity'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : null;
$method = $_SERVER['REQUEST_METHOD'];

// DEBUGGING: Loguear la entidad y el método recibidos en cada llamada
error_log("API Call Received: Method=" . $method . ", Entity=" . $entity . ", ID=" . ($id !== null ? $id : 'null'));

switch ($entity) {
    case 'login':
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            handleLogin($conn, $input['username'], $input['password']);
        } else {
            echo json_encode(['error' => 'Método no soportado para login']);
        }
        break;
    case 'admin_register_user': // Nueva entidad para el registro de usuarios por el admin
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            handleAdminRegisterUser($conn, $input);
        } else {
            echo json_encode(['error' => 'Método no soportado para admin_register_user']);
        }
        break;
    case 'users': // Nueva entidad para la gestión CRUD de usuarios (listar, editar, eliminar)
        handleUserManagement($conn, $method, $id);
        break;
    case 'backup':
        if ($method === 'GET') {
            handleBackup($servername, $username, $password, $dbname);
        } else {
            echo json_encode(['error' => 'Método no soportado para backup']);
        }
        break;
    default:
        // Procesar solicitudes GET, POST, PUT, DELETE para otras entidades genéricas
        switch ($method) {
            case 'GET':
                handleGet($conn, $entity, $id);
                break;
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                handlePost($conn, $entity, $data);
                break;
            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                handlePut($conn, $entity, $id, $data);
                break;
            case 'DELETE':
                handleDelete($conn, $entity, $id);
                break;
            default:
                echo json_encode(['error' => 'Método no soportado']);
                break;
        }
        break;
}

$conn->close();

/**
 * Maneja la lógica de inicio de sesión.
 * @param mysqli $conn Objeto de conexión a la base de datos.
 * @param string $username Nombre de usuario.
 * @param string $password Contraseña sin hashear.
 */
function handleLogin($conn, $username, $password) {
    $sql = "SELECT id, username, password_hash, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log('Login prepare statement error: ' . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error interno del servidor.']);
        return;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verificar la contraseña hasheada
        if (password_verify($password, $user['password_hash'])) {
            // Inicio de sesión exitoso
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso.', 'role' => $user['role']]);
        } else {
            // Contraseña incorrecta
            echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']);
        }
    } else {
        // Usuario no encontrado
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
    }
    $stmt->close();
}

/**
 * Maneja el registro de un nuevo usuario por parte de un administrador.
 * La contraseña se hashea antes de ser almacenada.
 * @param mysqli $conn Objeto de conexión a la base de datos.
 * @param array $data Datos del nuevo usuario (username, password, role).
 */
function handleAdminRegisterUser($conn, $data) {
    if (!isset($data['username']) || !isset($data['password']) || !isset($data['role'])) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos para el registro de usuario.']);
        return;
    }

    $username = $data['username'];
    $password = $data['password'];
    $role = $data['role'];

    // Hashear la contraseña antes de guardarla
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Verificar si el usuario ya existe
    $check_sql = "SELECT id FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya existe.']);
        $check_stmt->close();
        return;
    }
    $check_stmt->close();

    $sql = "INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log('Register user prepare statement error: ' . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de registro.']);
        return;
    }

    $stmt->bind_param("sss", $username, $hashed_password, $role);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente.', 'id' => $conn->insert_id]);
    } else {
        error_log('Register user execute statement error: ' . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario: ' . $stmt->error]);
    }
    $stmt->close();
}

/**
 * Maneja las operaciones CRUD para la entidad 'users'.
 * @param mysqli $conn Objeto de conexión a la base de datos.
 * @param string $method Método HTTP.
 * @param int|null $id ID del usuario (para GET, PUT, DELETE).
 */
function handleUserManagement($conn, $method, $id) {
    switch ($method) {
        case 'GET':
            handleGet($conn, 'users', $id);
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['username']) || !isset($data['role'])) {
                echo json_encode(['success' => false, 'message' => 'Datos incompletos para actualizar el usuario.']);
                return;
            }
            
            $username = $data['username'];
            $role = $data['role'];
            // CORRECCIÓN: Reemplazar ?? con isset() y operador ternario
            $password = isset($data['password']) ? $data['password'] : null; 

            $update_fields = ["username = ?", "role = ?"];
            $types = "ss";
            $values = [$username, $role];

            if ($password !== null && $password !== '') {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_fields[] = "password_hash = ?";
                $types .= "s";
                $values[] = $hashed_password;
            }

            $sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id = ?";
            $types .= "i";
            $values[] = $id;

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                error_log('Prepare statement error for PUT on users: ' . $conn->error);
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta para actualizar usuario: ' . $conn->error]);
                return;
            }
            call_user_func_array([$stmt, 'bind_param'], array_merge([$types], refValues($values)));

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'rows_affected' => $stmt->affected_rows]);
            } else {
                error_log('Execute statement error for PUT on users: ' . $stmt->error);
                echo json_encode(['success' => false, 'message' => 'Error al actualizar usuario: ' . $stmt->error]);
            }
            $stmt->close();
            break;
        case 'DELETE':
            handleDelete($conn, 'users', $id);
            break;
        default:
            echo json_encode(['error' => 'Método no soportado para la gestión de usuarios.']);
            break;
    }
}

/**
 * Maneja la solicitud de copia de seguridad de la base de datos.
 * Genera un archivo SQL de la base de datos y devuelve una URL de descarga.
 * @param string $servername Nombre del servidor de la base de datos.
 * @param string $username Nombre de usuario de la base de datos.
 * @param string $password Contraseña de la base de datos.
 * @param string $dbname Nombre de la base de datos.
 */
function handleBackup($servername, $username, $password, $dbname) {
    // Nombre del archivo de copia de seguridad
    $backupFile = $dbname . '_' . date('Y-m-d_H-i-s') . '.sql';
    $backupDir = __DIR__ . '/backups/'; // Directorio para guardar backups
    $backupPath = $backupDir . $backupFile;

    // Asegurarse de que el directorio de backups exista y sea escribible
    if (!is_dir($backupDir)) {
        if (!mkdir($backupDir, 0777, true)) {
            error_log('Error: No se pudo crear el directorio de backups: ' . $backupDir . '. Verifique los permisos.');
            echo json_encode(['success' => false, 'message' => 'Error: No se pudo crear el directorio de backups. Verifique los permisos de escritura en el servidor.']);
            return;
        }
    } else if (!is_writable($backupDir)) {
        error_log('Error: El directorio de backups no es escribible: ' . $backupDir . '. Verifique los permisos.');
        echo json_encode(['success' => false, 'message' => 'Error: El directorio de backups no es escribible. Verifique los permisos del directorio.']);
        return;
    }
    
    // Verificar si mysqldump existe en la ruta especificada
    $mysqldump_exists = false;
    $trimmed_path = trim(MY_SQLDUMP_PATH, '"'); // Eliminar comillas para verificar la existencia del archivo
    
    if (strpos($trimmed_path, '/') !== false || strpos($trimmed_path, '\\') !== false) {
        // Si la ruta contiene barras, asumimos que es una ruta completa
        if (file_exists($trimmed_path)) {
            $mysqldump_exists = true;
        }
    } else {
        // Si no contiene barras, asumimos que está en el PATH del sistema
        // Intentamos ejecutar un comando simple para ver si mysqldump es reconocido
        exec(MY_SQLDUMP_PATH . ' --version 2>&1', $version_output, $version_return);
        if ($version_return === 0) {
            $mysqldump_exists = true;
        }
    }

    if (!$mysqldump_exists) {
        error_log('Error: mysqldump no encontrado o no accesible en la ruta especificada: ' . MY_SQLDUMP_PATH);
        echo json_encode(['success' => false, 'message' => 'Error: mysqldump no encontrado o no accesible. Por favor, verifica la ruta en api.php y los permisos de ejecución.']);
        return;
    }

    // Construir el comando. Se redirige stderr (errores) a stdout (salida normal) para capturarlos en $output
    $command = MY_SQLDUMP_PATH . " --user={$username} --password={$password} --host={$servername} {$dbname} > {$backupPath} 2>&1"; 

    error_log('Executing command: ' . $command); // Loguea el comando exacto que se va a ejecutar

    $output = [];
    $returnVar = 0;
    
    // Ejecutar el comando
    exec($command, $output, $returnVar);

    error_log('mysqldump command output: ' . implode("\n", $output));
    error_log('mysqldump command return var: ' . $returnVar);

    if ($returnVar === 0) {
        // Si la copia de seguridad se creó correctamente, verificar si el archivo existe y tiene contenido
        if (file_exists($backupPath) && filesize($backupPath) > 0) {
            // Devolver una respuesta JSON con la URL de descarga del archivo
            // La URL asume que el directorio 'backups' es accesible públicamente
            $downloadUrl = 'backups/' . basename($backupFile);
            echo json_encode(['success' => true, 'download_url' => $downloadUrl, 'message' => 'Copia de seguridad creada exitosamente.']);
        } else {
            error_log('mysqldump ejecutado sin error, pero el archivo de backup está vacío o no existe: ' . $backupPath);
            echo json_encode(['success' => false, 'message' => 'Copia de seguridad generada, pero el archivo está vacío o no se guardó correctamente. Posibles causas: credenciales incorrectas, base de datos vacía, o problemas de permisos de escritura.']);
        }
    } else {
        // Error al generar la copia de seguridad
        $errorMessage = 'Error al ejecutar mysqldump. Código de retorno: ' . $returnVar . '. Mensaje: ' . implode("\n", $output);
        error_log($errorMessage);
        echo json_encode(['success' => false, 'message' => 'Error al generar la copia de seguridad: ' . $errorMessage]);
    }
}


/**
 * Maneja las solicitudes GET para obtener datos de una entidad.
 * @param mysqli $conn Objeto de conexión a la base de datos.
 * @param string $entity Nombre de la tabla.
 * @param int|null $id ID del registro a obtener (opcional).
 */
function handleGet($conn, $entity, $id) {
    $sql = "SELECT * FROM `$entity`";
    if ($id !== null) {
        $sql .= " WHERE id = " . intval($id);
    }
    $result = $conn->query($sql);

    if ($result) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        error_log('Query error for GET on ' . $entity . ': ' . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error al obtener datos de ' . $entity . ': ' . $conn->error]);
    }
}

/**
 * Maneja las solicitudes POST para insertar nuevos datos en una entidad.
 * @param mysqli $conn Objeto de conexión a la base de datos.
 * @param string $entity Nombre de la tabla.
 * @param array $data Datos a insertar.
 */
function handlePost($conn, $entity, $data) {
    if (empty($data)) {
        echo json_encode(['success' => false, 'message' => 'No se proporcionaron datos para insertar.']);
        return;
    }

    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    $sql = "INSERT INTO `$entity` ($columns) VALUES ($placeholders)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log('Prepare statement error for POST on ' . $entity . ': ' . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta para INSERT en ' . $entity . ': ' . $conn->error]);
        return;
    }

    $types = '';
    $values = [];
    foreach ($data as $value) {
        $types .= getTypeChar($value);
        $values[] = &$value; // Pasar por referencia
    }

    call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $values));

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        error_log('Execute statement error for POST on ' . $entity . ': ' . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Error al insertar datos en ' . $entity . ': ' . $stmt->error]);
    }
    $stmt->close();
}

/**
 * Maneja las solicitudes PUT para actualizar datos en una entidad.
 * @param mysqli $conn Objeto de conexión a la base de datos.
 * @param string $entity Nombre de la tabla.
 * @param int $id ID del registro a actualizar.
 * @param array $data Datos a actualizar.
 */
function handlePut($conn, $entity, $id, $data) {
    if (empty($data) || $id === null) {
        echo json_encode(['success' => false, 'message' => 'ID o datos no proporcionados para actualizar.']);
        return;
    }

    $setClauses = [];
    foreach ($data as $key => $value) {
        $setClauses[] = "`$key` = ?";
    }
    $setClause = implode(', ', $setClauses);
    $sql = "UPDATE `$entity` SET $setClause WHERE id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log('Prepare statement error for PUT on ' . $entity . ': ' . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta para UPDATE en ' . $entity . ': ' . $conn->error]);
        return;
    }

    $types = '';
    $values = [];
    foreach ($data as $value) {
        $types .= getTypeChar($value);
        $values[] = &$value;
    }
    $types .= 'i'; // Para el ID
    $values[] = &$id;

    call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $values));

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'rows_affected' => $stmt->affected_rows]);
    } else {
        error_log('Execute statement error for PUT on ' . $entity . ': ' . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Error al actualizar datos en ' . $entity . ': ' . $stmt->error]);
    }
    $stmt->close();
}

/**
 * Maneja las solicitudes DELETE para eliminar un registro de una entidad.
 * @param mysqli $conn Objeto de conexión a la base de datos.
 * @param string $entity Nombre de la tabla.
 * @param int $id ID del registro a eliminar.
 */
function handleDelete($conn, $entity, $id) {
    if ($id === null) {
        echo json_encode(['success' => false, 'message' => 'ID no proporcionado para eliminar.']);
        return;
    }

    $sql = "DELETE FROM `$entity` WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log('Prepare statement error for DELETE on ' . $entity . ': ' . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta para DELETE en ' . $entity . ': ' . $conn->error]);
        return;
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'rows_affected' => $stmt->affected_rows]);
    } else {
        error_log('Execute statement error for DELETE on ' . $entity . ': ' . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Error al eliminar datos en ' . $entity . ': ' . $stmt->error]);
    }
    $stmt->close();
}

/**
 * Obtiene el carácter de tipo para bind_param basado en el tipo de variable.
 * @param mixed $value El valor para determinar el tipo.
 * @return string Carácter de tipo ('i', 'd', 's', 'b').
 */
function getTypeChar($value) {
    if (is_int($value)) {
        return 'i';
    } elseif (is_float($value)) {
        return 'd';
    } elseif (is_string($value)) {
        return 's';
    } else {
        return 'b'; // blob (para otros tipos, aunque no se usará mucho aquí)
    }
}

/**
 * Función auxiliar para pasar referencias a bind_param en versiones antiguas de PHP (opcional, pero buena práctica).
 * @param array $arr El array de valores.
 * @return array El array con referencias.
 */
function refValues($arr) {
    if (strnatcmp(phpversion(), '5.3') >= 0) { // Versiones de PHP >= 5.3
        $refs = [];
        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }
    return $arr;
}
?>































