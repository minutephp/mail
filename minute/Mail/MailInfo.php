<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 7/7/2016
 * Time: 12:47 PM
 */
namespace Minute\Mail {

    use App\Model\MMail;
    use App\Model\MMailContent;
    use Minute\Cache\QCache;
    use Minute\Resolver\Resolver;

    class MailInfo {
        /**
         * @var Resolver
         */
        private $resolver;
        /**
         * @var QCache
         */
        private $qCache;

        /**
         * EventMailer constructor.
         *
         * @param Resolver $resolver
         * @param QCache $qCache
         */
        public function __construct(Resolver $resolver, QCache $qCache) {
            $this->resolver = $resolver;
            $this->qCache   = $qCache;
        }

        /**
         * @param $template
         *
         * @return MMail
         */
        public function getMailByName($template) {
            return MMail::where('name', '=', $template)->first();
        }

        /**
         * @param $where
         *
         * @return mixed
         */
        public function getMailByWhere($where) {
            return call_user_func_array([MMail::class, 'where'], $where)->first();
        }

        /**
         * @param MMail $mail
         * @param bool $random
         *
         * @return MMailContent
         */
        public function getMailContents($mail, $random = false) {
            /** @var MMailContent $model */
            $model = $random ? MMailContent::inRandomOrder() : MMailContent::class;

            if ($contents = $model->where('mail_id', '=', $mail->mail_id)->where('enabled', '=', 'true')->first()) {
                return $contents;
            }

            return null;
        }

        /**
         * @param MMail $mail
         *
         * @return MMailContent
         */
        public function getMailContent($mail) {
            return $this->getMailContents($mail, true);
        }
    }
}