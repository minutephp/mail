<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 10/11/2016
 * Time: 12:37 AM
 */
namespace Minute\Gmail {

    use Google_Client;
    use Google_Service_Gmail;
    use Google_Service_Oauth2;
    use Google_Service_Plus;
    use Minute\Config\Config;
    use Minute\Error\GmailError;
    use Minute\View\Redirection;

    class Client {
        const GMAIL_KEY = 'google/gmail';
        /**
         * @var int
         */
        protected $init = 0;
        /**
         * @var Config
         */
        private $config;
        /**
         * @var Google_Client
         */
        private $gmail;

        /**
         * Client constructor.
         *
         * @param Config $config
         * @param Google_Client $gmail
         */
        public function __construct(Config $config, Google_Client $gmail) {
            $this->config = $config;
            $this->gmail  = $gmail;
        }

        /**
         * @param array $overrides
         *
         * @return Google_Client
         * @throws GmailError
         */
        public function getGoogleClient(array $overrides = []) {
            if (!$this->init++) {
                $config = $this->getGmailConfig();
                $config = array_merge($config, $overrides);

                $this->gmail->setClientId($config['id']);
                $this->gmail->setClientSecret($config['secret']);
                $this->gmail->setScopes([Google_Service_Gmail::MAIL_GOOGLE_COM, Google_Service_Plus::USERINFO_EMAIL, Google_Service_Plus::USERINFO_PROFILE]);
                $this->gmail->setAccessType('offline');
                $this->gmail->setRedirectUri(sprintf("%s/admin/gmail/authorize", $this->config->getPublicVars('host')));
            }

            return $this->gmail;
        }

        public function startAuthorization($force = true) {
            $gmail = $this->getGoogleClient();

            if ($force) {
                $gmail->setApprovalPrompt('force');
            }

            $url   = $gmail->createAuthUrl();
            $redir = new Redirection($url);

            $redir->redirect();
        }

        public function verifyAuthorization(string $code) {
            $gmail = $this->getGoogleClient();

            try {
                $gmail->authenticate($code);

                if ($token = $gmail->getAccessToken()) {
                    $gmail    = new Google_Service_Oauth2($gmail);
                    $userInfo = $gmail->userinfo->get();
                    $replyTo  = sprintf('"%s" <%s>', trim(sprintf('%s %s', $userInfo->getGivenName(), $userInfo->getFamilyName())), $userInfo->email);

                    $this->config->set(self::GMAIL_KEY . "/replyTo", $replyTo, true);
                    $this->config->set(self::GMAIL_KEY . '/auth/token', $token, true);

                    return true;
                }
            } catch (\Exception $e) {
                throw new GmailError("Cannot authenticate gmail access. Make sure you have enabled access to Gmail API for your application. Error: " . $e->getMessage());
            }

            return false;
        }

        public function checkSavedAuthorization() {
            $gmail  = $this->getGoogleClient();
            $config = $this->getGmailConfig();

            if (!empty($config['token'])) {
                $gmail->setAccessToken($config['token']);

                if ($gmail->isAccessTokenExpired()) {
                    $gmail->refreshToken($config['refresh_token']);

                    if ($refreshToken = $gmail->getAccessToken()) {
                        $token = array_merge($config['token'], $refreshToken);
                        $this->config->set(self::GMAIL_KEY . '/auth/token', $token, true);
                    }
                }

                return true;
            } else {
                throw new GmailError("Gmail token is required for authorization");
            }
        }

        private function getGmailConfig() {
            if ($config = $this->config->get(self::GMAIL_KEY . '/auth')) {
                return $config;
            } else {
                throw new GmailError("Gmail credentials are missing in " . self::GMAIL_KEY);
            }
        }
    }
}