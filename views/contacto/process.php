<?php
// Ajustar configuración básica
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración básica con rutas absolutas
$to_email = "info@eccextremecloud.com";
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitización de datos
        $name = isset($_POST['name']) ? filter_var($_POST['name'], FILTER_SANITIZE_STRING) : '';
        $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
        $subject = isset($_POST['subject']) ? filter_var($_POST['subject'], FILTER_SANITIZE_STRING) : '';
        $message = isset($_POST['message']) ? filter_var($_POST['message'], FILTER_SANITIZE_STRING) : '';
        
        $errors = array();
        
        // Validaciones
        if (empty($name)) $errors[] = "El nombre es requerido";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido";
        if (empty($message)) $errors[] = "El mensaje es requerido";
        
        // Manejo de archivo CV
        $cv_path = '';
        if ($subject === 'rrhh' && isset($_FILES['cv']) && $_FILES['cv']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['cv'];
            
            // Crear directorio si no existe
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    throw new Exception("No se pudo crear el directorio de uploads");
                }
            }
            
            // Validaciones del archivo
            $allowed_types = array('application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file['type'], $allowed_types)) {
                $errors[] = "Tipo de archivo no permitido. Solo PDF o DOCX.";
            } elseif ($file['size'] > $max_size) {
                $errors[] = "El archivo excede el tamaño máximo permitido (5MB)";
            } else {
                $filename = time() . '_' . basename($file['name']);
                $cv_path = $upload_dir . $filename;
                
                if (!move_uploaded_file($file['tmp_name'], $cv_path)) {
                    throw new Exception("Error al subir el archivo");
                }
            }
        }
        
        // Envío del email si no hay errores
        if (empty($errors)) {
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: $name <$email>\r\n";
            
            $email_body = "
                <html>
                <body>
                    <h2>Nuevo mensaje de contacto</h2>
                    <p><strong>Nombre:</strong> {$name}</p>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Asunto:</strong> {$subject}</p>
                    <p><strong>Mensaje:</strong><br>{$message}</p>
            ";
            
            if ($cv_path) {
                $email_body .= "<p><strong>CV adjunto en:</strong> {$cv_path}</p>";
            }
            
            $email_body .= "</body></html>";
            
            if (!mail($to_email, "Nuevo contacto: $subject", $email_body, $headers)) {
                throw new Exception("Error al enviar el email");
            }
            
            $response = array(
                'success' => true,
                'message' => 'Mensaje enviado correctamente'
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Error en la validación',
                'errors' => $errors
            );
        }
    } else {
        throw new Exception("Método no permitido");
    }
} catch (Exception $e) {
    $response = array(
        'success' => false,
        'message' => 'Error en el servidor: ' . $e->getMessage()
    );
}

// Enviar respuesta JSON
header('Content-Type: application/json');
// Añadir log de errores
if (!empty($errors)) {
    error_log("Errores de formulario: " . print_r($errors, true));
}

// Añadir headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
echo json_encode($response);
exit;
?>