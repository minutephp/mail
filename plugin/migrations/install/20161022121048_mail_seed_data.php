<?php

use Phinx\Migration\AbstractMigration;

class MailSeedData extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {

		$this->execute('insert ignore into `m_events` (`name`, `handler`, `data`, `priority`, `comments`, `plugin`) values (\'user.send.password\', \'Minute\\\\EventHandler\\\\EventMailer@sendMail\', \'user_password_reset\', null, \'Send Password Reset Email\', \'mail\')');
		$this->execute('insert ignore into `m_mails` (`created_at`, `updated_at`, `type`, `name`, `description`) values (NOW(), NOW(), \'account\', \'user_password_reset\', \'Password Reset Email\')');
		$this->execute('insert ignore into `m_mail_contents` (`mail_id`, `subject`, `text`, `html`, `attachment`, `embed_images`, `track_opens`, `track_clicks`, `unsubscribe_link`, `enabled`) values ((select mail_id from m_mails where name = "user_password_reset" limit 1), \'Reset your {site_name} password\', \'Hi {first_name},We received a request to reset the password for your account.If you requested a password reset for {site_name}\\\'s account, click the button below. \\n{auth}/create-password{signature}P.S. If you didn’t make this request, please ignore this email.\', \'<p>Hi {first_name},</p><p>We received a request to reset the password for your account.</p><p>If you requested a password reset for {site_name}\\\'s account, click the button below.&nbsp;</p><p></p><a href=\\"{auth}/create-password\\"><img src=\\"//i.imgur.com/fPtAGeP.png\\" alt=\\"More information\\"></a><br><br>{signature}<br><br>P.S. If you didn’t make this request, please ignore this email.\', null, \'true\', \'true\', \'true\', \'false\', \'true\')');
		$this->execute('insert ignore into `m_events` (`name`, `handler`, `data`, `priority`, `comments`, `plugin`) values (\'user.social.signup\', \'Minute\\\\EventHandler\\\\EventMailer@sendMail\', \'user_password_create\', null, \'Tell user to create a password\', \'mail\')');
		$this->execute('insert ignore into `m_mails` (`created_at`, `updated_at`, `type`, `name`, `description`) values (NOW(), NOW(), \'account\', \'user_password_create\', \'Password Reset Email\')');
		$this->execute('insert ignore into `m_mail_contents` (`mail_id`, `subject`, `text`, `html`, `attachment`, `embed_images`, `track_opens`, `track_clicks`, `unsubscribe_link`, `enabled`) values ((select mail_id from m_mails where name = "user_password_create" limit 1), \'Create your {site_name} password\', \'Hi {first_name},Thanks for signing up for {site_name} via {provider}.In addition to {provider}, you can also access your {site_name} account using your email in futureEmail: {email}Password: not set (click the button below to create a password)\\r\\n{auth}/create-password{signature}P.S. You can always use your %provider% login to access your %site_name% account. This step is only for your own convenience. \', \'<p>Hi {first_name},</p><p>Thanks for signing up for {site_name} via {provider}.</p><p>In addition to {provider}, you can also access your {site_name} account using your email in future</p><p>Email: {email}<br>Password: not set (click the button below to create a password)</p><p></p><a href=\\"{auth}/create-password\\"><img src=\\"//i.imgur.com/fPtAGeP.png\\" alt=\\"More information\\"></a><br><br>{signature}<br><br>P.S. You can always use your %provider% login to access your %site_name% account. This step is only for your own convenience.&nbsp;\', null, \'true\', \'true\', \'true\', \'false\', \'true\')');



    }
}