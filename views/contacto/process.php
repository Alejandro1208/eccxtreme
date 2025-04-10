<?php
// Ajustar configuración básica
error_reporting(E_ALL);
ini_set('display_errors', 1);

$to_email = "info@eccextremecloud.com";

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitización de datos básicos
        $name = isset($_POST['name']) ? filter_var($_POST['name'], FILTER_SANITIZE_STRING) : '';
        $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
        $subject = isset($_POST['subject']) ? filter_var($_POST['subject'], FILTER_SANITIZE_STRING) : '';
        $message = isset($_POST['message']) ? filter_var($_POST['message'], FILTER_SANITIZE_STRING) : '';
        
        $errors = array();
        
        // Validaciones básicas
        if (empty($name)) $errors[] = "El nombre es requerido";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido";
        if (empty($message)) $errors[] = "El mensaje es requerido";
        
        // Manejo del archivo CV
        if ($subject === 'rrhh' && isset($_FILES['cv']) && $_FILES['cv']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['cv'];
            
            // Validaciones del archivo
            $allowed_types = array('application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file['type'], $allowed_types)) {
                $errors[] = "Tipo de archivo no permitido. Solo PDF o DOCX.";
            } elseif ($file['size'] > $max_size) {
                $errors[] = "El archivo excede el tamaño máximo permitido (5MB)";
            }
        }
        
        // Envío del email si no hay errores
        if (empty($errors)) {
            // Generar un boundary único para el email
            $boundary = md5(time());
            
            // Headers del email
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "From: $name <$email>\r\n";
            $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
            
            // Cuerpo del mensaje
            $message_body = "--$boundary\r\n";
            $message_body .= "Content-Type: text/html; charset=UTF-8\r\n";
            $message_body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $message_body .= "
                <html>
                <body>
                    <h2>Nuevo mensaje de contacto</h2>
                    <p><strong>Nombre:</strong> {$name}</p>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Asunto:</strong> {$subject}</p>
                    <p><strong>Mensaje:</strong><br>{$message}</p>
                </body>
                </html>\r\n";
            
            // Adjuntar CV si existe
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                $file_content = file_get_contents($_FILES['cv']['tmp_name']);
                $file_name = $_FILES['cv']['name'];
                $file_type = $_FILES['cv']['type'];
                
                $message_body .= "--$boundary\r\n";
                $message_body .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
                $message_body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
                $message_body .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $message_body .= chunk_split(base64_encode($file_content)) . "\r\n";
            }
            
            $message_body .= "--$boundary--";
            
            // Enviar email
            if (!mail($to_email, "Nuevo contacto: $subject", $message_body, $headers)) {
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

// Headers y respuesta
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
echo json_encode($response);
exit;
?>