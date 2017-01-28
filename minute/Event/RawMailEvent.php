<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 7/8/2016
 * Time: 10:04 AM
 */
namespace Minute\Event {

    use Swift_Message;

    class RawMailEvent extends Event {
        const MAIL_SEND_RAW = "mail.send.raw";
        /**
         * @var Swift_Message
         */
        private $message;

        /**
         * @var bool
         */
        private $handled = false;

        /**
         * RawMailEvent constructor.
         *
         * @param Swift_Message $message
         */
        public function __construct(Swift_Message $message) {
            $this->message = $message;
        }

        /**
         * @return boolean
         */
        public function isHandled() {
            return $this->handled;
        }
        
        /**
         * @param boolean $handled
         *
         * @return RawMailEvent
         */
        public function setHandled($handled) {
            $this->handled = $handled;

            return $this;
        }

        /**
         * @return Swift_Message
         */
        public function getMessage() {
            return $this->message;
        }
    }
}