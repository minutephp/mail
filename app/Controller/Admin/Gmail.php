<?php
/**
 * Created by: MinutePHP framework
 */
namespace App\Controller\Admin {

    use Google_Client;
    use Minute\Gmail\Client;
    use Minute\View\Redirection;
    use Minute\View\View;

    class Gmail {
        /**
         * @var Google_Client
         */
        protected $gmail;
        /**
         * @var Client
         */
        private $client;

        /**
         * Gmail constructor.
         *
         * @param Client $client
         */
        public function __construct(Client $client) {
            $this->client = $client;
        }

        public function start() {
            $this->client->startAuthorization();
        }

        public function auth(string $code) {
            $this->client->verifyAuthorization($code);

            return new Redirection('/admin/gmail/setup');
        }
    }
}