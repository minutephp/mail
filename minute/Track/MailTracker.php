<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 10/12/2016
 * Time: 5:09 AM
 */
namespace Minute\Track {

    use App\Model\MMailStat;
    use Minute\Cache\QCache;
    use Minute\Event\Event;
    use Minute\Event\UserEvent;
    use Minute\Http\HttpRequestEx;
    use Minute\Http\HttpResponseEx;

    class MailTracker {
        const MAX_QUEUE_LENGTH = 100;
        /**
         * @var HttpResponseEx
         */
        private $response;
        /**
         * @var HttpRequestEx
         */
        private $request;
        /**
         * @var QCache
         */
        private $cache;

        /**
         * MailTracker constructor.
         *
         * @param HttpResponseEx $response
         * @param HttpRequestEx $request
         * @param QCache $cache
         */
        public function __construct(HttpResponseEx $response, HttpRequestEx $request, QCache $cache) {
            $this->response = $response;
            $this->request  = $request;
            $this->cache    = $cache;
        }

        public function sent(UserEvent $event) {
            if ($mail_content_id = $event->getData()) {
                $memcached = $this->cache->getType() === 'memcached';
                $queue     = $memcached ? $this->cache->get('mails_sent', function () { return []; }) : [];

                $queue[$mail_content_id] = ($queue[$mail_content_id] ?? 0) + 1;

                if (!$memcached || (count($queue) > self::MAX_QUEUE_LENGTH)) {
                    foreach ($queue as $mail_content_id => $count) {
                        $this->increment('sent', $mail_content_id, $count);
                    }

                    $queue = [];
                }

                if ($memcached) {
                    $this->cache->set('mails_sent', $queue);
                }
            }
        }

        public function open(Event $event) {
            $this->track('opens', $event->getData() ?: 0);
        }

        public function click(Event $event) {
            $this->track('clicks', $event->getData() ?: 0);
        }

        public function unsubscribe(Event $event) {
            $this->track('unsubscribes', $event->getData() ?: 0);
        }

        public function conversion() {
            $mails = $this->getMailsFromCookie('opens');

            if (!empty($mails)) {
                $this->increment('conversions', $mails[count($mails) - 1]);
            }
        }

        protected function track($type, int $mail_content_id) {
            $mails = $this->getMailsFromCookie($type);

            if (empty($mails) && !in_array($mail_content_id, $mails)) {
                $mails[] = $mail_content_id;
                $this->response->setCookie("mail_$type", json_encode($mails), '+1 year');
                $this->increment($type, $mail_content_id);
            }
        }

        protected function increment($type, int $mail_content_id, int $count = 1) {
            if (!empty($mail_content_id)) {
                MMailStat::unguard(true);

                /** @var MMailStat $record */
                $record = MMailStat::firstOrNew(['mail_content_id' => $mail_content_id]);
                $record->$type += $count;
                $record->save();
            }
        }

        protected function getMailsFromCookie($type) {
            $cookie = $this->request->getCookie("mail_$type", '[]');
            $mails  = json_decode($cookie, true);

            return $mails ?: [];
        }
    }
}