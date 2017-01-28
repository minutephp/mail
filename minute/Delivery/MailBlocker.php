<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 11/6/2016
 * Time: 12:31 AM
 */
namespace Minute\Delivery {

    use App\Model\MMailBlock;
    use App\Model\MMailUnsubscribe;
    use Carbon\Carbon;
    use Minute\Event\Dispatcher;
    use Minute\Event\MailInfoEvent;
    use Minute\Event\UserMailEvent;
    use Minute\Mail\MailTypes;

    class MailBlocker {
        /**
         * @var Dispatcher
         */
        private $dispatcher;
        /**
         * @var MailTypes
         */
        private $mailTypes;

        /**
         * MailBlocker constructor.
         *
         * @param Dispatcher $dispatcher
         * @param MailTypes $mailTypes
         */
        public function __construct(Dispatcher $dispatcher, MailTypes $mailTypes) {
            MMailBlock::unguard(true);
            MMailUnsubscribe::unguard(true);

            $this->dispatcher = $dispatcher;
            $this->mailTypes  = $mailTypes;
        }

        public function bounce(UserMailEvent $event) {
            if ($user = $event->getUser()) {
                $this->block($event);

                $user->verified = 'false';
                $user->save();
            }
        }

        public function spam(UserMailEvent $event) {
            if ($user = $event->getUser()) {
                $this->block($event);
            }
        }

        public function unsubscribeAll(UserMailEvent $event) {
            $now   = Carbon::now();
            $types = $this->mailTypes->getMailTypes();

            foreach ($types as $type) {
                try {
                    MMailUnsubscribe::create(['user_id' => $event->getUserId(), 'mail_type' => $type, 'created_at' => $now]);
                } catch (\Throwable $e) {
                }
            }

            $this->dispatcher->fire(UserMailEvent::USER_MAIL_UNSUBSCRIBED, $event);
        }

        protected function block(UserMailEvent $event) {
            try {
                $data = $event->getUserData();
                MMailBlock::create(['email' => $data['email']]);
            } catch (\Throwable $e) {
                //already blocked!
            } finally {
                $this->unsubscribeAll($event);
            }
        }
    }
}