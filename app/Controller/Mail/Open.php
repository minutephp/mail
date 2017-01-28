<?php
/**
 * Created by: MinutePHP framework
 */
namespace App\Controller\Mail {

    use Minute\Routing\RouteEx;
    use Minute\View\Helper;
    use Minute\View\View;

    class Open {

        public function index() { //the actual logging is done using USER_MAIL_OPEN event
            header('Content-Type: image/gif');
            echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
            exit(0);
        }
    }
}