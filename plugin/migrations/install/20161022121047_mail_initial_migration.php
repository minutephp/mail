<?php
use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class MailInitialMigration extends AbstractMigration
{
    public function change()
    {
        // Automatically created phinx migration commands for tables from database minute

        // Migration for table m_mail_blocks
        $table = $this->table('m_mail_blocks', array('id' => 'mail_block_id'));
        $table
            ->addColumn('email', 'string', array('limit' => 255))
            ->addIndex(array('email'), array('unique' => true))
            ->create();


        // Migration for table m_mail_contents
        $table = $this->table('m_mail_contents', array('id' => 'mail_content_id'));
        $table
            ->addColumn('mail_id', 'integer', array('limit' => 11))
            ->addColumn('subject', 'string', array('limit' => 255))
            ->addColumn('text', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
            ->addColumn('html', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
            ->addColumn('attachment', 'string', array('null' => true, 'limit' => 255))
            ->addColumn('embed_images', 'enum', array('default' => 'true', 'values' => array('true','false')))
            ->addColumn('track_opens', 'enum', array('default' => 'false', 'values' => array('false','true')))
            ->addColumn('track_clicks', 'enum', array('default' => 'false', 'values' => array('false','true')))
            ->addColumn('unsubscribe_link', 'enum', array('default' => 'true', 'values' => array('false','true')))
            ->addColumn('enabled', 'enum', array('default' => 'true', 'values' => array('false','true')))
            ->addIndex(array('mail_id'), array())
            ->create();


        // Migration for table m_mail_gmail
        $table = $this->table('m_mail_gmail', array('id' => 'mail_gmail_id'));
        $table
            ->addColumn('fingerprint', 'string', array('limit' => 32))
            ->addColumn('handled', 'enum', array('default' => 'false', 'values' => array('true','false')))
            ->addIndex(array('fingerprint'), array('unique' => true))
            ->create();


        // Migration for table m_mail_stats
        $table = $this->table('m_mail_stats', array('id' => 'mail_stat_id'));
        $table
            ->addColumn('mail_content_id', 'integer', array('limit' => 11))
            ->addColumn('sent', 'integer', array('default' => '0', 'limit' => 11))
            ->addColumn('opens', 'integer', array('default' => '0', 'limit' => 11))
            ->addColumn('clicks', 'integer', array('default' => '0', 'limit' => 11))
            ->addColumn('unsubscribes', 'integer', array('default' => '0', 'limit' => 11))
            ->addColumn('conversions', 'integer', array('default' => '0', 'limit' => 11))
            ->addIndex(array('mail_content_id'), array('unique' => true))
            ->create();


        // Migration for table m_mail_unsubscribes
        $table = $this->table('m_mail_unsubscribes', array('id' => 'mail_unsubscribe_id'));
        $table
            ->addColumn('user_id', 'integer', array('limit' => 11))
            ->addColumn('mail_type', 'enum', array('values' => array('account','billing','support','tip','offer','announcement','other')))
            ->addColumn('created_at', 'datetime', array())
            ->addIndex(array('user_id', 'mail_type'), array('unique' => true))
            ->create();


        // Migration for table m_mails
        $table = $this->table('m_mails', array('id' => 'mail_id'));
        $table
            ->addColumn('created_at', 'datetime', array())
            ->addColumn('updated_at', 'datetime', array())
            ->addColumn('type', 'string', array('null' => true, 'limit' => 30))
            ->addColumn('name', 'string', array('limit' => 255))
            ->addColumn('description', 'string', array('null' => true, 'limit' => 255))
            ->addIndex(array('name'), array('unique' => true))
            ->create();


    }
}