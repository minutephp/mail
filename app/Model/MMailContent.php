<?php
/**
 * User: Sanchit <dev@minutephp.com>
 * Date: 6/22/2016
 * Time: 8:22 AM
 */
namespace App\Model {

    use Minute\Model\ModelEx;

    class MMailContent extends ModelEx {
        protected $table      = 'm_mail_contents';
        protected $primaryKey = 'mail_content_id';
    }
}