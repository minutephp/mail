<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 11/6/2016
 * Time: 12:37 AM
 */
namespace Minute\Mail {

    use App\Model\MMail;
    use Minute\Cache\QCache;
    use Minute\Event\ImportEvent;
    use Minute\Model\CollectionEx;

    class MailTypes {
        /**
         * @var QCache
         */
        private $cache;

        /**
         * MailTypes constructor.
         *
         * @param QCache $cache
         */
        public function __construct(QCache $cache) {
            $this->cache = $cache;
        }

        public function getMailTypesWithHints(ImportEvent $event) {
            $types = $this->getMailTypes();

            $hints = [
                'account' => 'Account related emails',
                'billing' => 'Billing related emails',
                'support' => 'Support replies and updates',
                'tip' => 'Usability tips and blog posts',
                'offer' => 'Offers and discounts',
                'announcement' => 'Site updates / new features',
                'other' => 'Other site related news',
            ];

            $event->setContent(['types' => array_unique(array_merge(array_keys($hints), $types ?? [])), 'hints' => $hints]);
        }

        public function getMailTypes() {
            $types = $this->cache->get('mail-types', function () {
                /** @var CollectionEx $mails */
                $mails = MMail::select('type')->distinct()->get();

                return $mails->pluck('type')->toArray();
            }, 86400);

            return $types ?? [];
        }
    }
}