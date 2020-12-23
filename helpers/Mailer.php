<?php

namespace app\helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private $mail;

    private function init()
    {
        $this->mail = new PHPMailer(true);
        try {
            //Server settings
            $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $this->mail->isSMTP();
            $this->mail->Host       = 'in-v3.mailjet.com';
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = '8c47f641f098b77bdb31c55fefc341ca';
            $this->mail->Password   = '93444dd9dec2a2a3d7cdbf5cbf6474d4';
            #Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            // $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = 587;
            $this->mail->CharSet = PHPMailer::CHARSET_UTF8;
            $this->mail->setFrom('no-reply@sandbox.iwasi.com.pe', 'iWasi');
        } catch (Exception $ex) {
            echo $this->mail->ErrorInfo;
        }
    }

    /**
     * Envía correo electrónico
     *
     * @param [string|array] $address
     * @param [string] $subject
     * @param [string|html] $body
     * @param [string|array] $attachment
     * @param [string|array] $cc
     * @param [string|array] $bcc
     * @return bool
     */
    public function send($address, $subject, $body, $attachment = null, $cc = null, $bcc = null)
    {
        $this->init();
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $this->mail->Body = $body;

        #destinatarios
        if (is_array($address)) {
            foreach ($address as $destinatario) {
                $this->mail->addAddress($destinatario, "");
            }
        } else {
            $this->mail->addAddress($address, "");
        }

        #correos en copia
        if (is_array($cc) && $cc !== null) {
            foreach ($cc as $destinatario) {
                $this->mail->addCC($destinatario);
            }
        } elseif ($cc !== null) {
            $this->mail->addCC($cc);
        }

        #correos en copia oculta
        if (is_array($bcc) && $bcc !== null) {
            foreach ($bcc as $destinatario) {
                $this->mail->addCC($destinatario);
            }
        } elseif ($bcc !== null) {
            $this->mail->addCC($bcc, "");
        }

        #adjunta archivo(s)
        if (is_array($attachment)) {
            foreach ($attachment as $file) {
                $this->mail->AddAttachment($file);
            }
        } elseif ($attachment !== null) {
            $this->mail->AddAttachment($attachment);
        }

        if (!$this->mail->send()) {
            $message = $this->mail->ErrorInfo;
        } else {
            $message = "Mensaje Enviado!";
        }

        $this->mail->ClearAddresses();

        return $message;
    }
}
