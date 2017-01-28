<?php
/**
 * Created by: MinutePHP Framework
 */
namespace App\Model {

    use Minute\Model\ModelEx;

    class MMailStat extends ModelEx {
        protected $table      = 'm_mail_stats';
        protected $primaryKey = 'mail_stat_id';
    }
}