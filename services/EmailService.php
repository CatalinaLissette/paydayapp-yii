<?php


namespace app\services;


use Yii;
use yii\base\Component;
use yii\base\Exception;

class EmailService
{
    /**
     * Envía un correo electrónico.
     *
     * @param string $to      Dirección de correo electrónico del destinatario.
     * @param string $subject Asunto del correo electrónico.
     * @param string $body    Cuerpo del correo electrónico en formato HTML.
     *
     * @return bool True si el correo electrónico se envió correctamente, de lo contrario, false.
     */
    public function sendEmail($to, $subject, $body)
    {
        $mailer = Yii::$app->mailer;


        $email = $mailer->compose();
        $email->setFrom('payggodev03@gmail.com');
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setHtmlBody($body);

        try {
            return $email->send();
        } catch (Exception $e) {
            Yii::error("Error al enviar el correo electrónico: " . $e->getMessage());
            return false;
        }
    }

}