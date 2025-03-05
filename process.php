<?php
// Configuración para el hosting
$to_email = "info@eccextremecloud.com"; // Verifica que este email esté correcto
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/"; // Ruta absoluta en el hosting

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Configuración
    $to_email = "info@eccextremecloud.com";
    $upload_dir = "uploads/";
    
    // Validación y sanitización de datos
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
    
    $errors = [];
    
    // Validaciones básicas
    if (empty($name)) $errors[] = "El nombre es requerido";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido";
    if (empty($message)) $errors[] = "El mensaje es requerido";
    
    // Validación específica para RRHH
    if ($subject === 'rrhh') {
        if (!isset($_FILES['cv']) || $_FILES['cv']['error'] === UPLOAD_ERR_NO_FILE) {
            $errors[] = "Debe adjuntar su CV para postulaciones de RRHH";
        }
    }
    
    // Manejo de archivo adjunto para RRHH
    $cv_path = '';
    if ($subject === 'rrhh' && isset($_FILES['cv'])) {
        $file = $_FILES['cv'];
        
        // Validar tipo de archivo
        $allowed_types = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = "Tipo de archivo no permitido. Solo PDF o DOCX.";
        }
        
        // Validar tamaño (5MB máximo)
        if ($file['size'] > 5 * 1024 * 1024) {
            $errors[] = "El archivo excede el tamaño máximo permitido (5MB)";
        }
        
        // Si no hay errores, mover el archivo
        if (empty($errors)) {
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $filename = time() . '_' . basename($file['name']);
            $cv_path = $upload_dir . $filename;
            
            if (!move_uploaded_file($file['tmp_name'], $cv_path)) {
                $errors[] = "Error al subir el archivo";
            }
        }
    }
    
    // Si no hay errores, enviar email
    if (empty($errors)) {
        // Modificar el envío de email para incluir el dominio del hosting
        $headers = "From: formulario@" . $_SERVER['HTTP_HOST'] . "\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $email_message = "<html><body>";
        $email_message .= "<h2>Nuevo mensaje de contacto</h2>";
        $email_message .= "<p><strong>Nombre:</strong> $name</p>";
        $email_message .= "<p><strong>Email:</strong> $email</p>";
        $email_message .= "<p><strong>Asunto:</strong> $subject</p>";
        $email_message .= "<p><strong>Mensaje:</strong><br>$message</p>";
        
        if ($cv_path) {
            $email_message .= "<p><strong>CV adjunto:</strong> " . basename($cv_path) . "</p>";
        }
        
        $email_message .= "</body></html>";
        
        $mail_subject = "Nuevo contacto: $subject";
        
        if (mail($to_email, $mail_subject, $email_message, $headers)) {
            $response = [
                'success' => true,
                'message' => 'Mensaje enviado correctamente'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Error al enviar el mensaje'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Error en la validación',
            'errors' => $errors
        ];
    }
    
    // Devolver respuesta en JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}