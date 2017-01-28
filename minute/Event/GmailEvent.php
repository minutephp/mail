<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 10/11/2016
 * Time: 4:36 AM
 */
namespace Minute\Event {

    class GmailEvent extends Event {
        const GMAIL_NEW_EMAIL = 'gmail.new.email';
        /**
         * @var array
         */
        private $from;
        /**
         * @var string
         */
        private $subject;
        /**
         * @var string
         */
        private $text;
        /**
         * @var string
         */
        private $html;
        /**
         * @var bool
         */
        private $handled = false;
        /**
         * @var array
         */
        private $ref;
        /**
         * @var string
         */
        private $time;

        /**
         * GmailEvent constructor.
         *
         * @param array $from
         * @param string $subject
         * @param string $text
         * @param string $html
         * @param string $ref
         * @param string $time
         */
        public function __construct(array $from, string $subject, string $text, string $html, string $ref, string $time) {
            $this->from    = $from;
            $this->subject = $subject;
            $this->text    = $text;
            $this->html    = $html;
            $this->ref     = $ref;
            $this->time    = $time;
        }

        /**
         * @return string
         */
        public function getRef() {
            return $this->ref;
        }

        /**
         * @param string $ref
         *
         * @return GmailEvent
         */
        public function setRef($ref): GmailEvent {
            $this->ref = $ref;

            return $this;
        }

        /**
         * @return string
         */
        public function getTime(): string {
            return $this->time;
        }

        /**
         * @param string $time
         *
         * @return GmailEvent
         */
        public function setTime(string $time): GmailEvent {
            $this->time = $time;

            return $this;
        }

        /**
         * @return boolean
         */
        public function isHandled(): bool {
            return $this->handled;
        }

        /**
         * @param boolean $handled
         *
         * @return GmailEvent
         */
        public function setHandled(bool $handled): GmailEvent {
            $this->handled = $handled;

            return $this;
        }

        /**
         * @return array
         */
        public function getFrom(): array {
            return $this->from;
        }

        /**
         * @param array $from
         *
         * @return GmailEvent
         */
        public function setFrom(array $from): GmailEvent {
            $this->from = $from;

            return $this;
        }

        /**
         * @return string
         */
        public function getSubject(): string {
            return $this->subject;
        }

        /**
         * @param string $subject
         *
         * @return GmailEvent
         */
        public function setSubject(string $subject): GmailEvent {
            $this->subject = $subject;

            return $this;
        }

        /**
         * @return string
         */
        public function getText(): string {
            return $this->text;
        }

        /**
         * @param string $text
         *
         * @return GmailEvent
         */
        public function setText(string $text): GmailEvent {
            $this->text = $text;

            return $this;
        }

        /**
         * @return string
         */
        public function getHtml(): string {
            return $this->html;
        }

        /**
         * @param string $html
         *
         * @return GmailEvent
         */
        public function setHtml(string $html): GmailEvent {
            $this->html = $html;

            return $this;
        }
    }
}