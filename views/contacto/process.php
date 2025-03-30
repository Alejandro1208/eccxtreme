<?php
// Configuración básica
$to_email = "info@eccextremecloud.com";
$upload_dir = dirname(__FILE__) . "/uploads/";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitización de datos
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
    
    $errors = [];
    
    // Validaciones
    if (empty($name)) $errors[] = "El nombre es requerido";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido";
    if (empty($message)) $errors[] = "El mensaje es requerido";
    
    // Manejo de archivo CV
    $cv_path = '';
    if ($subject === 'rrhh' && isset($_FILES['cv']) && $_FILES['cv']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['cv'];
        
        // Crear directorio si no existe
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Validaciones del archivo
        $allowed_types = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = "Tipo de archivo no permitido. Solo PDF o DOCX.";
        } elseif ($file['size'] > $max_size) {
            $errors[] = "El archivo excede el tamaño máximo permitido (5MB)";
        } else {
            $filename = time() . '_' . basename($file['name']);
            $cv_path = $upload_dir . $filename;
            
            if (!move_uploaded_file($file['tmp_name'], $cv_path)) {
                $errors[] = "Error al subir el archivo";
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
        
        $mail_sent = mail($to_email, "Nuevo contacto: $subject", $email_body, $headers);
        
        if ($mail_sent) {
            $response = [
                'success' => true,
                'message' => 'Mensaje enviado correctamente'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Error al enviar el mensaje. Por favor, intente más tarde.'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Error en la validación',
            'errors' => $errors
        ];
    }
    
    // Enviar respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>