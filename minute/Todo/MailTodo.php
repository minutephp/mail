<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 11/5/2016
 * Time: 11:04 AM
 */
namespace Minute\Todo {

    use App\Model\MEvent;
    use App\Model\MMail;
    use Minute\Config\Config;
    use Minute\Event\ImportEvent;

    class MailTodo {
        /**
         * @var TodoMaker
         */
        private $todoMaker;

        /**
         * MailerTodo constructor.
         *
         * @param TodoMaker $todoMaker - This class is only called by TodoEvent (so we assume TodoMaker is be available)
         */
        public function __construct(TodoMaker $todoMaker, Config $config) {
            $this->todoMaker = $todoMaker;
        }

        public function getTodoList(ImportEvent $event) {
            $templates = ['user_account_verify', 'user_password_reset'];
            $hints     = ['user_account_verify' => 'Sent after signup to confirm email account (and optionally create account password)',
                          'user_password_reset' => 'Forgot password retrieval email (to reset account password)'];

            $handlers = MEvent::where('handler', 'like', '%EventMailer@sendMail%')->get();
            $comments = [];

            foreach ($handlers as $handler) {
                if ($template = $handler->data) {
                    $templates[]         = $template;
                    $comments[$template] = $handler->comments;
                }
            }

            foreach (array_unique($templates) as $template) {
                $todos[] = ['name' => "Write $template email", 'description' => $hints[$template] ?? $comments[$template] ?? '',
                            'status' => MMail::where('name', '=', $template)->count() ? 'complete' : 'incomplete', 'link' => '/admin/mails'];
            }

            $event->addContent(['Mails' => $todos ?? []]);
        }
    }
}