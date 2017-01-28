<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 7/7/2016
 * Time: 12:27 PM
 */
namespace Minute\Event {

    class UserMailEvent extends UserEvent {
        //action events - if triggered causes an action like sending outs actual email
        const USER_SEND_EMAIL = "user.send.email";

        //notification events (for logging, etc)
        const USER_MAIL_SENT         = "user.mail.sent";
        const USER_MAIL_LINK_CLICK   = "user.mail.link.click";
        const USER_MAIL_OPEN         = "user.mail.open";
        const USER_MAIL_UNSUBSCRIBED = "user.mail.unsubscribed";
        const USER_MAIL_SUPPRESSED   = "user.mail.suppressed";

        const USER_MAIL_BOUNCED    = "user.mail.bounced";
        const USER_MAIL_SPAM_CLICK = "user.mail.spam.click";

        const USER_MAIL_UNSUBSCRIBE_FORCE = "user.mail.unsubscribe.force";

        /**
         * @var bool
         */
        private $handled = false;

        /**
         * @return boolean
         */
        public function isHandled(): bool {
            return $this->handled;
        }

        /**
         * @param boolean $handled
         *
         * @return UserMailEvent
         */
        public function setHandled(bool $handled): UserMailEvent {
            $this->handled = $handled;

            return $this;
        }
    }
}