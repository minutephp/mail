<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 1/8/2017
 * Time: 11:10 AM
 */
namespace Minute\Site {

    use Minute\Config\Config;
    use Minute\Error\BasicError;
    use Minute\Event\Dispatcher;
    use Minute\Event\ExceptionEvent;
    use Minute\Event\RawMailEvent;

    class ErrorMailer {
        /**
         * @var Dispatcher
         */
        private $dispatcher;
        /**
         * @var \Swift_Message
         */
        private $message;
        /**
         * @var Config
         */
        private $config;

        /**
         * ErrorMailer constructor.
         *
         * @param Config $config
         * @param Dispatcher $dispatcher
         * @param \Swift_Message $message
         */
        public function __construct(Config $config, Dispatcher $dispatcher, \Swift_Message $message) {
            $this->config     = $config;
            $this->dispatcher = $dispatcher;
            $this->message    = $message;
        }

        public function sendAlert(ExceptionEvent $event) {
            $error = $event->getError();

            if ($error instanceof BasicError) {
                $severity = $error->getSeverity();

                if (($severity == BasicError::CRITICAL) || ($severity == BasicError::EMERGENCY)) {
                    $details   = $error->getTraceAsString();
                    $domain    = $this->config->getPublicVars('domain');
                    $toEmail   = $this->config->get('private/owner_email', sprintf('webmaster@%s', $domain));
                    $signature = $this->config->getPublicVars('signature', "- $domain mailer");
                    $fromEmail = sprintf('noreply@%s', $domain);

                    $this->message->setFrom($fromEmail);
                    $this->message->setTo($toEmail);
                    $this->message->setSubject("[Urgent] Error on $domain ($severity)");
                    $this->message->setBody("A $severity error has occurred on $domain\n\nError details:\n$details\n\n$signature");

                    $rawMailEvent = new RawMailEvent($this->message);
                    $this->dispatcher->fire(RawMailEvent::MAIL_SEND_RAW, $rawMailEvent);
                }
            }
        }
    }
}