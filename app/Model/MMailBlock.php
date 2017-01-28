<?php
/**
 * Created by: MinutePHP Framework
 */
namespace App\Model {

    use Minute\Model\ModelEx;

    class MMailBlock extends ModelEx {
        protected $table      = 'm_mail_blocks';
        protected $primaryKey = 'mail_block_id';
    }
}