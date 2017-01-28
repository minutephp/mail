<?php
/**
 * Created by: MinutePHP Framework
 */
namespace App\Model {

    use Minute\Model\ModelEx;

    class MMailUnsubscribe extends ModelEx {
        protected $table      = 'm_mail_unsubscribes';
        protected $primaryKey = 'mail_unsubscribe_id';
    }
}