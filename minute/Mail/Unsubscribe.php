<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 7/7/2016
 * Time: 12:57 PM
 */
namespace Minute\Mail {

    use App\Model\MMailBlock;
    use App\Model\MMailUnsubscribe;
    use Minute\Cache\QCache;
    use Minute\Resolver\Resolver;

    class Unsubscribe {
        /**
         * @var Resolver
         */
        private $resolver;
        /**
         * @var QCache
         */
        private $qCache;

        /**
         * UnsubscribeCheck constructor.
         *
         * @param Resolver $resolver
         * @param QCache $qCache
         */
        public function __construct(Resolver $resolver, QCache $qCache) {
            $this->resolver = $resolver;
            $this->qCache   = $qCache;
        }

        public function isBlocked(string $email) {
            $count = $this->qCache->get("mail-blocked-$email", function () use ($email) {
                return MMailBlock::where('email', '=', $email)->count();
            }, 3600);

            return $count > 0;
        }

        public function isUnsubscribed(int $user_id, string $mailType) {
            $return = $this->qCache->get("mail-user-unsubscribed-$user_id-$mailType", function () use ($user_id, $mailType) {
                $count = MMailUnsubscribe::where('user_id', '=', $user_id)->where('mail_type', '=', $mailType)->count();

                return $count > 0;
            }, 86400);

            return $return;
        }
    }
}