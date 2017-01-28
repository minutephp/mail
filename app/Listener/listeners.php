<?php

/** @var Binding $binding */
use Minute\Delivery\MailBlocker;
use Minute\Event\AdminEvent;
use Minute\Event\Binding;
use Minute\Event\ExceptionEvent;
use Minute\Event\GmailEvent;
use Minute\Event\MailInfoEvent;
use Minute\Event\TodoEvent;
use Minute\Event\UserMailEvent;
use Minute\EventHandler\EventMailer;
use Minute\Mail\MailTypes;
use Minute\Menu\MailMenu;
use Minute\Panel\MailPanel;
use Minute\Site\ErrorMailer;
use Minute\Todo\MailTodo;
use Minute\Track\MailTracker;
use Minute\User\VerifyUser;

$binding->addMultiple([
    //['event' => UserSendPassword::USER_SEND_PASSWORD, 'handler' => [EventMailer::class, 'sendMail'], 'data' => 'user_forgot_email'],
    ['event' => AdminEvent::IMPORT_ADMIN_MENU_LINKS, 'handler' => [MailMenu::class, 'adminLinks']],
    ['event' => AdminEvent::IMPORT_ADMIN_DASHBOARD_PANELS, 'handler' => [MailPanel::class, 'adminDashboardPanel']],

    //if member's plugin is installed
    ['event' => 'import.members.profile.tabs', 'handler' => [MailMenu::class, 'profileTabs']],

    //mail owner about critical errors
    ['event' => ExceptionEvent::EXCEPTION_UNHANDLED, 'handler' => [ErrorMailer::class, 'sendAlert']],

    //actually send mail to user
    ['event' => UserMailEvent::USER_SEND_EMAIL, 'handler' => [EventMailer::class, 'sendMail']],

    //account verification by email reply by mail
    ['event' => GmailEvent::GMAIL_NEW_EMAIL, 'handler' => [VerifyUser::class, 'verify'], 'priority' => 100],

    //tracking
    ['event' => UserMailEvent::USER_MAIL_SENT, 'handler' => [MailTracker::class, 'sent']],
    ['event' => UserMailEvent::USER_MAIL_OPEN, 'handler' => [MailTracker::class, 'open']],
    ['event' => UserMailEvent::USER_MAIL_LINK_CLICK, 'handler' => [MailTracker::class, 'click']],
    ['event' => UserMailEvent::USER_MAIL_UNSUBSCRIBED, 'handler' => [MailTracker::class, 'unsubscribe']],

    ['event' => UserMailEvent::USER_MAIL_BOUNCED, 'handler' => [MailBlocker::class, 'bounce']],
    ['event' => UserMailEvent::USER_MAIL_SPAM_CLICK, 'handler' => [MailBlocker::class, 'spam']],
    ['event' => UserMailEvent::USER_MAIL_UNSUBSCRIBE_FORCE, 'handler' => [MailBlocker::class, 'unsubscribeAll']],

    //get mail types
    ['event' => MailInfoEvent::IMPORT_MAIL_TYPES, 'handler' => [MailTypes::class, 'getMailTypesWithHints']],

    //tasks
    ['event' => TodoEvent::IMPORT_TODO_ADMIN, 'handler' => [MailTodo::class, 'getTodoList']],

    //payment related
    ['event' => 'user.wallet.first.payment', 'handler' => [MailTracker::class, 'conversion']],

]);
