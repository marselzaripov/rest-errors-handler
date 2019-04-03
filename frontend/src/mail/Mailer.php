<?php
/**
 * Mailer.php
 */

namespace App\mail;

use Yii;
use yii\swiftmailer\Mailer as SwiftMailer;
use yii\mail\MessageInterface;

/**
 * Отправитель email. Отличается от родителя тем, что дописывает в файл логов.
 */
class Mailer extends SwiftMailer
{
    /**
     * Saves the message to a file under [[fileTransportPath]]. Differs from parent by appending message to log file.
     *
     * @param MessageInterface $message Message to send.
     *
     * @return bool whether the message is saved successfully
     */
    protected function saveMessage($message)
    {
        $path = Yii::getAlias($this->fileTransportPath);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if ($this->fileTransportCallback !== null) {
            $file = $path . '/' . call_user_func($this->fileTransportCallback, $this, $message);
        } else {
            $file = $path . '/' . $this->generateMessageFileName();
        }
        file_put_contents($file, $message->toString() . "\n\n", FILE_APPEND | LOCK_EX);

        return true;
    }
}
