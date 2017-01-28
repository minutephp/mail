<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 10/11/2016
 * Time: 4:22 AM
 */
namespace App\Controller\Cron {

    use App\Model\MMailGmail;
    use Google_Service_Gmail;
    use Google_Service_Gmail_Message;
    use Google_Service_Gmail_MessagePart;
    use Google_Service_Gmail_ModifyMessageRequest;
    use Minute\Config\Config;
    use Minute\Crypto\Blowfish;
    use Minute\Event\Dispatcher;
    use Minute\Event\GmailEvent;
    use Minute\Gmail\Client;
    use Minute\GMail\Utils;
    use Minute\Mail\Extractor;

    class GmailCron {
        const MESSAGE_LIMIT = 3;
        /**
         * @var Client
         */
        private $client;
        /**
         * @var Utils
         */
        private $utils;
        /**
         * @var Dispatcher
         */
        private $dispatcher;
        /**
         * @var Blowfish
         */
        private $blowfish;
        /**
         * @var Config
         */
        private $config;
        /**
         * @var Extractor
         */
        private $extractor;

        /**
         * GmailCron constructor.
         *
         * @param Config $config
         * @param Client $client
         * @param Utils $utils
         * @param Dispatcher $dispatcher
         * @param Blowfish $blowfish
         * @param Extractor $extractor
         */
        public function __construct(Config $config, Client $client, Utils $utils, Dispatcher $dispatcher, Blowfish $blowfish, Extractor $extractor) {
            $this->config     = $config;
            $this->client     = $client;
            $this->utils      = $utils;
            $this->dispatcher = $dispatcher;
            $this->blowfish   = $blowfish;
            $this->extractor  = $extractor;
        }

        public function checkMessages() {
            if ($this->client->checkSavedAuthorization()) {
                MMailGmail::unguard(true);

                $gmail    = new Google_Service_Gmail($this->client->getGoogleClient());
                $emailIds = [];
                $seen     = 0;

                do {
                    $response  = $gmail->users_messages->listUsersMessages('me', empty($pageToken) ? [] : ['pageToken' => $pageToken]);
                    $messages  = $response->getMessages();
                    $pageToken = $response->getNextPageToken();

                    /** @var Google_Service_Gmail_Message $message */
                    foreach ($messages as $message) {
                        if ($id = $message->getId()) {
                            if (!MMailGmail::where('fingerprint', '=', $id)->first()) {
                                $emailIds[] = $id;

                                if (count($emailIds) > self::MESSAGE_LIMIT) {
                                    break(2);
                                }
                            } elseif ($seen++ > 5) {
                                break(2);
                            }
                        }
                    }
                } while ($pageToken && !empty($messages));

                if (!empty($emailIds)) {
                    $handled = false;

                    foreach ($emailIds as $emailId) {
                        /** @var Google_Service_Gmail_Message $email */
                        /** @var Google_Service_Gmail_MessagePart $payload */
                        try {
                            $email      = $gmail->users_messages->get('me', $emailId, ['format' => 'full']);
                            $payload    = $email->getPayload();
                            $parts      = $payload->getParts();
                            $headers    = $payload->getHeaders();
                            $fromEmail  = $this->utils->getHeader($headers, 'From');
                            $emailTime  = $this->utils->getHeader($headers, 'Date');
                            $subject    = $this->utils->getHeader($headers, 'Subject');
                            $emailBody  = $this->utils->decodeEmail($gmail, $emailId, $parts ?: [$payload]);
                            $references = $this->utils->getHeader($headers, 'References');
                            $emailInfo  = $this->extractor->extractEmail($fromEmail);

                            if (!empty($references) && preg_match(sprintf('/(\w+)\\@%s/', $this->config->getPublicVars('domain')), $references, $matches)) {
                                if ($decrypt = $this->blowfish->decrypt($matches[1])) {
                                    $reference = $decrypt;
                                }
                            }

                            $event = new GmailEvent($emailInfo, $subject, $emailBody['text'] ?? '', $emailBody['html'] ?? '', $reference ?? '', $emailTime);
                            $this->dispatcher->fire(GmailEvent::GMAIL_NEW_EMAIL, $event);
                            $handled = $event->isHandled();

                            MMailGmail::updateOrCreate(['fingerprint' => $emailId], ['handled' => $handled ? 'true' : 'false']);
                        } catch (\Exception $e) {
                            echo '';
                        } finally {
                            try {
                                if ($handled) {
                                    $mods = new Google_Service_Gmail_ModifyMessageRequest();
                                    $mods->setRemoveLabelIds(['INBOX']);
                                    $gmail->users_messages->modify('me', $emailId, $mods);
                                }
                            } catch (\Exception $e) {
                            }
                        }
                    }
                }
            }
        }
    }
}