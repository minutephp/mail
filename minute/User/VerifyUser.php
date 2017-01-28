<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 10/12/2016
 * Time: 3:13 AM
 */
namespace Minute\User {

    use App\Model\User;
    use Minute\Event\GmailEvent;

    class VerifyUser {
        public function verify(GmailEvent $event) {
            if (!$event->isHandled()) {
                if (preg_match('/^V(\d+)$/', $event->getRef(), $matches)) {
                    /** @var User $user */
                    if ($user = User::find($matches[1])) {
                        $user->verified = 'true';
                        $event->setHandled($user->save() ? true : false);
                    }
                }
            }
        }
    }
}