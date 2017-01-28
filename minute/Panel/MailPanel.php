<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 7/8/2016
 * Time: 7:57 PM
 */
namespace Minute\Panel {

    use App\Model\MMailUnsubscribe;
    use Carbon\Carbon;
    use Minute\Event\ImportEvent;

    class MailPanel {
        public function adminDashboardPanel(ImportEvent $event) {
            $yesterday = Carbon::create(date('Y'), date('m'), date('d'), 0, 0, 0, 'UTC');
            $count  = MMailUnsubscribe::where('created_at', '>', $yesterday)->count();
            $panels = [
                ['type' => 'negative', 'title' => 'Unsubscribes', 'stats' => "$count unsubscribes", 'icon' => 'fa-envelope-o', 'priority' => 80, 'href' => '/admin/plugins', 'cta' => 'Email stats..',
                 'bg' => 'bg-red']
            ];

            $event->addContent($panels);
        }
    }
}