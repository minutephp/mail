<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 7/7/2016
 * Time: 12:26 PM
 */

namespace Minute\EventHandler {

    use App\Model\User;
    use Html2Text\Html2Text;
    use Minute\Config\Config;
    use Minute\Event\Dispatcher;
    use Minute\Event\RawMailEvent;
    use Minute\Event\UserEvent;
    use Minute\Event\UserMailEvent;
    use Minute\Gmail\Client;
    use Minute\Http\Browser;
    use Minute\Mail\Extractor;
    use Minute\Mail\MailInfo;
    use Minute\Mail\Unsubscribe;
    use Minute\Track\UserTracker;
    use StringTemplate\Engine;
    use Swift_Attachment;
    use Swift_Image;
    use Swift_Message;

    class EventMailer {
        /**
         * @var MailInfo
         */
        private $mailInfo;
        /**
         * @var Unsubscribe
         */
        private $unsubscribe;
        /**
         * @var Config
         */
        private $config;
        /**
         * @var Swift_Message
         */
        private $swiftMessage;
        /**
         * @var Engine
         */
        private $replacer;
        /**
         * @var UserTracker
         */
        private $tracker;
        /**
         * @var Dispatcher
         */
        private $dispatcher;
        /**
         * @var Html2Text
         */
        private $html2Text;
        /**
         * @var Browser
         */
        private $downloader;
        /**
         * @var Extractor
         */
        private $extractor;

        /**
         * EventMailer constructor.
         *
         * @param MailInfo $mailInfo
         * @param Unsubscribe $unsubscribe
         * @param Config $config
         * @param Swift_Message $swiftMessage
         * @param Engine $replacer
         * @param UserTracker $tracker
         * @param Dispatcher $dispatcher
         * @param Html2Text $html2Text
         * @param Browser $downloader
         * @param Extractor $extractor
         */
        public function __construct(MailInfo $mailInfo, Unsubscribe $unsubscribe, Config $config, Swift_Message $swiftMessage, Engine $replacer, UserTracker $tracker, Dispatcher $dispatcher,
                                    Html2Text $html2Text, Browser $downloader, Extractor $extractor) {
            $this->mailInfo     = $mailInfo;
            $this->unsubscribe  = $unsubscribe;
            $this->config       = $config;
            $this->swiftMessage = $swiftMessage;
            $this->replacer     = $replacer;
            $this->tracker      = $tracker;
            $this->dispatcher   = $dispatcher;
            $this->html2Text    = $html2Text;
            $this->downloader   = $downloader;
            $this->extractor    = $extractor;
        }

        public function sendMail(UserEvent $event) {
            $user_id = $event->getUserId();

            /** @var User $user */
            if ($user = User::find($user_id)) {
                if ($data = $event->getData()) {
                    if ($mail = is_array($data) ? $this->mailInfo->getMailByWhere($data) : $this->mailInfo->getMailByName($data)) {
                        $email = $user->contact_email ?: $user->email;

                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $content = $this->mailInfo->getMailContent($mail);

                            if ($content && $content->subject) {
                                //mail without unsubscribe link like forgot-password etc are not checked for un-subscriptions
                                if (($content->unsubscribe_link === 'true') && (($user->verified !== 'true') || $this->unsubscribe->isUnsubscribed($user_id, $mail->type))) {
                                    $this->dispatcher->fire(UserMailEvent::USER_MAIL_SUPPRESSED, $event);

                                    return null;
                                }

                                if (!$this->unsubscribe->isBlocked($email)) {
                                    $config     = $this->config->get('private/site/email', []);
                                    $content_id = $content->mail_content_id;

                                    $publicKeys = $this->config->getPublicVars();
                                    $userFields = $user->toArray();
                                    $userData   = $event->getUserData();
                                    $defaults   = [
                                        'full_name' => trim("$user->first_name $user->last_name"),
                                        'signature' => sprintf("%s support", $publicKeys['site_name']),
                                        'first_name_space' => empty($user->first_name) || preg_match('/^member/i', $user->first_name) ? '' : ucwords(strtolower($user->first_name))
                                    ];

                                    $replacements = array_merge($publicKeys, $userData, $userFields, $defaults);

                                    unset($replacements['password']);

                                    $to   = [$email => $defaults['full_name']];
                                    $from = ($config['from'] ?? null) ?: [sprintf('support@%s', $publicKeys['domain']) => sprintf('%s support', $publicKeys['site_name'])];

                                    $subject = $this->replacer->render($content->subject, $replacements);
                                    $message = Swift_Message::newInstance()->setFrom($from)->setTo($to)->setSubject($subject);

                                    $htmlBody = $this->replacer->render($content->html, $replacements);

                                    if (!empty($content->text)) {
                                        $textBody = $this->replacer->render($content->text, $replacements);
                                    } else {
                                        $this->html2Text->setHtml($htmlBody);
                                        $textBody = $this->html2Text->getText();
                                    }

                                    if ($content->embed_images === 'true') {
                                        $htmlBody = preg_replace_callback('/(<img.*?src=)(["\'])(http.*?)\\2/', function ($matches) use ($message) {
                                            $cached = $this->downloader->downloadCached($matches[3]);
                                            $embed  = $message->embed(Swift_Image::fromPath($cached));

                                            return sprintf('%s%s%s%s', $matches[1], $matches[2], $embed, $matches[2]);
                                        }, $htmlBody);
                                    }

                                    if ($content->track_opens === 'true') {
                                        $htmlBody .= PHP_EOL . '<p><img src="{open}/_mail/open" width="1" height="1"></p>';
                                    }

                                    if ($content->track_clicks === 'true') {
                                        $textBody = preg_replace('/\b(?<!\{auth\})http/', '{track}http', $textBody);
                                        $htmlBody = preg_replace_callback('/(<a.*?href=(["\']))(?!\{auth\})([^"\']+)(\\2)/i', function ($matches) {
                                            $str = $matches[1] . '{track}' . urlencode($matches[3]) . $matches[4];

                                            return $str;
                                        }, $htmlBody);
                                    }

                                    if ($content->unsubscribe_link === 'true') {
                                        $unsubscribeText = $this->config->get('private/site/unsubscribe', 'To change subscription settings:');
                                        $unsubscribeLink = '{unsub}/members/subscriptions';
                                        $textBody        .= "\n\n$unsubscribeText\n$unsubscribeLink\n\n";
                                        $htmlBody        .= "\n\n" . sprintf('<p' . '>&nbsp;</p><p><a href="%s">%s</a></p>', $unsubscribeLink, $unsubscribeText);
                                    }

                                    $authTags = [
                                        'auth' => $this->tracker->createAuthUrl($user_id, UserMailEvent::USER_MAIL_LINK_CLICK, $content_id),
                                        'unsub' => $this->tracker->createAuthUrl($user_id, UserMailEvent::USER_MAIL_UNSUBSCRIBED, $content_id),
                                        'track' => $this->tracker->createTrackingUrl($user_id, UserMailEvent::USER_MAIL_LINK_CLICK, $content_id),
                                        'open' => $this->tracker->createTrackingUrl($user_id, UserMailEvent::USER_MAIL_OPEN, $content_id)
                                    ];

                                    $htmlBody = $this->replacer->render($htmlBody, $authTags);
                                    $textBody = $this->replacer->render($textBody, $authTags);

                                    /** @var Swift_Message $message */
                                    $message->setBody($htmlBody, 'text/html');
                                    $message->addPart($textBody, 'text/plain');

                                    if ($replyTo = $userData['replyTo'] ?? $this->config->get(Client::GMAIL_KEY . '/replyTo') ?? null) {
                                        $message->setReplyTo($this->extractor->asArray($replyTo));
                                    }

                                    if ($reference = $userData['reference'] ?? null) {
                                        $headers = $message->getHeaders();
                                        $headers->addTextHeader('References', sprintf('<%s@%s>', trim($userData['reference'], '<>'), $publicKeys['domain']));
                                    }

                                    if ($attachment = $content->attachment) {
                                        if (filter_var($attachment, FILTER_VALIDATE_URL)) {
                                            $attachment = $this->downloader->downloadCached($attachment);
                                        }

                                        $message->attach(Swift_Attachment::fromPath($attachment));
                                    }

                                    $rawMailEvent = new RawMailEvent($message);
                                    $this->dispatcher->fire(RawMailEvent::MAIL_SEND_RAW, $rawMailEvent);

                                    if (is_callable([$event, 'setHandled'])) {
                                        $event->setHandled($rawMailEvent->isHandled());
                                    }

                                    $event->setData($content_id);
                                    $this->dispatcher->fire(UserMailEvent::USER_MAIL_SENT, $event);

                                    return $message; //only for testing (otherwise doesn't need to return anything)
                                } else {
                                    $this->dispatcher->fire(UserMailEvent::USER_MAIL_SUPPRESSED, $event);
                                }
                            }
                        }
                    }
                }
            }

            return null;
        }

    }
}