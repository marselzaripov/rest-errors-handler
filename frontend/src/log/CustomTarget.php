<?php
/**
 * CustomTarget.php
 */

namespace frontend\src\log;

use Yii;
use yii\log\Target;
use yii\log\Logger;
use yii\mail\MailerInterface;
use App\sms\SmsSenderInterface;
use yii\mail\MessageInterface;
use yii\base\InvalidConfigException;
use yii\log\LogRuntimeException;
use yii\di\Instance;
use yii\helpers\VarDumper;

/**
 * Класс таргета логов, реализующий отправку email или sms в зависимости от уровня ошибки - warning или error.
 */
class CustomTarget extends Target
{
    /**
     * @var array Конфигурация для [[MessageInterface|message]].
     */
    public $message = [];

    /**
     * @var array Конфиг для отправки sms.
     */
    public $sms = [];

    /**
     * @var MailerInterface|array|string Объeкт отправителя email или id компонента отправителя.
     */
    public $mailer = 'mailer';

    /**
     * @var SmsSenderInterface|array|string Объeкт отправителя sms или id компонента отправителя.
     */
    public $smsSender = 'sms';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if (empty($this->message['subject'])) {
            $this->message['subject'] = 'Errors on yii2-logger.loc';
        }
        if (empty($this->message['to'])) {
            throw new InvalidConfigException('The "to" option must be set for CustomTarget::message.');
        }
        if (empty($this->sms['to'])) {
            throw new InvalidConfigException('The "to" option must be set for CustomTarget::sms.');
        }
        $this->mailer = Instance::ensure($this->mailer, 'yii\mail\MailerInterface');
        $this->smsSender = Instance::ensure($this->smsSender, 'frontend\src\sms\SmsSenderInterface');
    }

    /**
     * Отправка сообщений об ошибках по смс (уровень error) или email (уровень warning).
     * Реализовано через запись в файл.
     *
     * @return void
     * @throws LogRuntimeException
     */
    public function export(): void
    {
        foreach ($this->messages as $message) {
            $level = $message[1];
            $message = $this->formatMessage($message);
            if ($level === Logger::LEVEL_ERROR) {
                $result = $this->smsSender->sendSms($this->sms['to'], $message);
            } else if ($level === Logger::LEVEL_WARNING) {
                $message = $this->composeEmailMessage($message);
                $result = $message->send($this->mailer);
            }
            if (isset($result) && $result === false) {
                throw new LogRuntimeException('Unable to export log from ' . __CLASS__);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function formatMessage($message): string
    {
        list($text, $level,, $timestamp) = $message;
        $level = Logger::getLevelName($level);
        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $text = (string) $text;
            } else {
                $text = VarDumper::export($text);
            }
        }
        $traces = [];
        if (isset($message[4])) {
            foreach ($message[4] as $trace) {
                $traces[] = "in {$trace['file']}:{$trace['line']}";
            }
        }
        $prefix = $this->getMessagePrefix($message);
        return $this->getTime($timestamp) . " {$prefix}[$level] $text"
            . (empty($traces) ? '' : "\n    " . implode("\n    ", $traces));
    }

    /**
     * Возвращает объект email сообщения.
     *
     * @param string $body Содержимое письма.
     *
     * @return MessageInterface $message
     */
    protected function composeEmailMessage($body): MessageInterface
    {
        $message = $this->mailer->compose();
        Yii::configure($message, $this->message);
        $message->setTextBody($body);

        return $message;
    }
}
