<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 10/11/2016
 * Time: 1:07 AM
 */
namespace Minute\GMail {

    use Google_Service_Gmail_MessagePart;
    use Google_Service_Gmail_MessagePartBody;
    use Htmlawed;
    use Minute\Config\Config;
    use Minute\Crypto\JwtEx;
    use Minute\Event\Dispatcher;
    use Minute\Event\UserUploadEvent;
    use Minute\Log\LoggerEx;

    class Utils { //probably swiftmailer can parse this better???
        /**
         * @var Dispatcher
         */
        private $dispatcher;
        /**
         * @var LoggerEx
         */
        private $logger;
        /**
         * @var Config
         */
        private $config;

        /**
         * MailUtils constructor.
         *
         * @param Dispatcher $dispatcher
         * @param LoggerEx $logger
         * @param Config $config
         */
        public function __construct(Dispatcher $dispatcher, LoggerEx $logger, Config $config) {
            $this->dispatcher = $dispatcher;
            $this->logger     = $logger;
            $this->config     = $config;
        }

        public function getHeader($headers, $name) {
            if ($r = array_filter($headers, function ($f) use ($name) { return $f['name'] === $name; })) {
                return array_shift($r)['value'];
            }

            return false;
        }

        public function decodeEmail($gmail, $emailId, $parts) {
            /** @var Google_Service_Gmail_MessagePart $part */
            /** @var Google_Service_Gmail_MessagePartBody $body */

            $results = ['messages' => [], 'attachments' => []];

            for ($j = count($parts) - 1; $j >= 0; $j--) {
                $part = $parts[$j];

                try {
                    if ($more = $part->getParts()) {
                        if ($decoded = self::decodeEmail($gmail, $emailId, $more)) {
                            $results['messages']    = array_merge($results['messages'], $decoded['messages']);
                            $results['attachments'] = array_merge($results['attachments'], $decoded['attachments']);
                        }
                    } elseif ($fn = $part->getFilename()) {
                        $body = $gmail->users_messages_attachments->get('me', $emailId, $part->getBody()->getAttachmentId());

                        $results['attachments'][] = ['content' => base64_decode(strtr($body->getData(), '-_', '+/')), 'file' => $fn];
                    } elseif ($body = $part->getBody()) {
                        $results['messages'][] = ['content' => base64_decode(strtr($body->getData(), '-_', '+/')), 'type' => $part->getMimeType()];
                    }
                } catch (\Exception $e) {
                }
            }

            /*            usort($results['messages'], function ($a, $b) {
                            $points = function ($r) { return $r == 'text/html' ? 2 : ($r == 'text/plain' ? 3 : 1); };

                            return @($points($b['type']) - $points($a['type']));
                        });*/

            foreach ($results['messages'] as $message) {
                if (!empty($message['content'])) {
                    if ($message['type'] === 'text/plain') {
                        $results['text'] = trim($message['content']);
                    } elseif ($message['type'] === 'text/html') {
                        $results['html'] = trim($message['content']);
                    }
                }
            }

            if (!empty($results['attachments'])) {
                $results['message'] .= "\nAttachments:";

                foreach ($results['attachments'] as $attachment) {
                    if (!empty($attachment['file']) && !empty($attachment['content'])) {
                        try {
                            $event = new UserUploadEvent(0, $attachment['content'], $attachment['file'], 'content');
                            $this->dispatcher->fire(UserUploadEvent::USER_UPLOAD_CONTENT, $event);

                            if ($url = $event->getUrl()) {
                                $results['message'] .= sprintf('%s<' . 'a href="%s">%s</a>', PHP_EOL, $url, basename($url));
                            }
                        } catch (\Throwable $e) {
                            $this->logger->warn("Unable to upload attachment for gmail");
                        }
                    }
                }
            }

            return $results;
        }
    }
}