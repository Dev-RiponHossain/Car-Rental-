<?php
require_once 'includes/config.php';

class USER
{
   /* private $db;
    
    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            exit("Error: " . $e->getMessage());
        }
    }*/

    public function sendMail($email, $message, $subject)
    {
        require_once 'mailer/PHPMailer.php';
        require_once 'mailer/SMTP.php';
        

        $mail = new PHPMailer\PHPMailer\PHPMailer(); // Enable exceptions

       
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'moynulislamshimanto11@gmail.com';
            $mail->Password   = 'fkismgljfuellmim'; 
            $mail->Port       = 587;
            // Recipients
            $mail->setFrom('moynulislamshimanto11@gmail.com', 'Car Rental System');
            $mail->addAddress($email);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            return $mail->send();           
            
    }
}
?>