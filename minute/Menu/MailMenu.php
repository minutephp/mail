<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 7/8/2016
 * Time: 7:57 PM
 */
namespace Minute\Menu {

    use Minute\Event\ImportEvent;

    class MailMenu {
        public function adminLinks(ImportEvent $event) {
            $links = [
                'mails' => ['title' => 'Emails', 'icon' => 'fa-envelope', 'priority' => 4],
                'mailTemplates' => ['title' => 'Mail templates', 'icon' => 'fa-envelope-o', 'priority' => 1, 'parent' => 'mails', 'href' => '/admin/mails'],
            ];

            $event->addContent($links);
        }

        public function profileTabs(ImportEvent $event) {
            $tabs = [["href" => "/members/subscriptions", "label" => "Subscriptions", "icon" => "fa-envelope", "priority" => 2]];

            $event->addContent($tabs);
        }
    }
}