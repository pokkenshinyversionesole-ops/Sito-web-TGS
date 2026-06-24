<?php
// Esempio backend per inviare il messaggio a tycherosgroups@outlook.it
// Installare PHPMailer: composer require phpmailer/phpmailer

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contatti.html');
    exit;
}

$name = trim($_POST['name'] ?? '');
$surname = trim($_POST['surname'] ?? '');
$userEmail = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');
$subject = trim($_POST['_subject'] ?? 'Nuovo messaggio dal sito');

if (!$userEmail || !$message) {
    echo 'Per favore compila tutti i campi richiesti.';
    exit;
}

$mail = new PHPMailer(true);
try {
    // Configura il server SMTP (modifica con i tuoi dati)
    $mail->isSMTP();
    $mail->Host = 'smtp.example.com';            // es: smtp.office365.com
    $mail->SMTPAuth = true;
    $mail->Username = 'smtp_user@example.com';  // il tuo account SMTP
    $mail->Password = 'smtp_password';          // la password SMTP
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // 'tls' o PHPMailer::ENCRYPTION_SMTPS
    $mail->Port = 587;                          // 587 per TLS, 465 per SSL

    // Mittente (deve essere un indirizzo del tuo dominio per evitare SPF/DMARC)
    $mail->setFrom('noreply@tuodominio.com', 'Sito Tycheros');

    // Destinatario
    $mail->addAddress('tycherosgroups@outlook.it', 'Tycheros Groups');

    // Imposta Reply-To al contatto dell'utente (così riceverete risposte all'email dell'utente)
    if (filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $mail->addReplyTo($userEmail, $name . ' ' . $surname);
    }

    // Contenuto
    $mail->isHTML(false);
    $mail->Subject = $subject;
    $body = "Nome: {$name} {$surname}\nEmail: {$userEmail}\n\nMessaggio:\n{$message}";
    $mail->Body = $body;

    $mail->send();

    // Redirect a una pagina di ringraziamento
    header('Location: grazie.html');
    exit;
} catch (Exception $e) {
    echo "Errore invio: {$mail->ErrorInfo}";
}
