<?php
/**
 * SimpleSmsSender.php
 */

namespace frontend\src\sms;

use Yii;
use yii\base\BaseObject;
use yii\helpers\FileHelper;
use yii\base\Exception;

/**
 * Простейший отправитель sms. Имплементирует отправку сообщения в файл.
 */
class SimpleSmsSender extends BaseObject implements SmsSenderInterface
{
    /**
     * @var string Путь к файлу лога. Можно задавать в конфиге.
     */
    public $logFile;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->logFile === null) {
            $this->logFile = Yii::$app->getRuntimePath() . '/logs/sms.txt';
        } else {
            $this->logFile = Yii::getAlias($this->logFile);
        }
    }

    /**
     * Отправляет sms сообщение.
     *
     * @param string|array $phone   Номер получателя.
     * @param string       $message Текст сообщения.
     *
     * @return bool Возвращает true в случае успешной записи в файл.
     * @throws Exception Если не хватает прав на создание директории.
     */
    public function sendSms($phone, $message): bool
    {
        $logPath = dirname($this->logFile);
        FileHelper::createDirectory($logPath);
        if (!is_array($phone)) {
            $phone = (array) $phone;
        }
        $phone = implode(', ', $phone);
        $message = "[{$phone}]: {$message} \n\n";

        return @file_put_contents($this->logFile, $message, FILE_APPEND | LOCK_EX);
    }
}
