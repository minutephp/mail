<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 10/12/2016
 * Time: 4:41 AM
 */
namespace Minute\Mail {

    class Extractor {
        public function asArray($email) {
            if (is_string($email)) {
                $parts = $this->extractEmail($email);

                return !empty($parts['email']) ? [$parts['email'] => $parts['full_name']] : $email;
            }

            return $email;
        }

        public function extractEmail(string $str) {
            $emails = array();

            if (preg_match('/\s*"?([^><"]+)"?\s*((?:<[^><]+>)?)\s*/', $str, $matches)) {
                $name  = !empty($matches[2]) ? $matches[1] : 'Member';
                $email = !empty($matches[2]) ? $matches[2] : $matches[1];
                $parts = explode(' ', $name, 2);

                return ['email' => trim($email, '<>'), 'full_name' => $name, 'first_name' => @$parts[0] ?: '', 'last_name' => @$parts[1] ?: ''];
            }

            return $emails;
        }

    }
}