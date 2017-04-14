<?php

/** @var Router $router */
use Minute\Model\Permission;
use Minute\Routing\Router;

$router->get('/admin/mails', null, 'admin', 'm_mails[5] as mails ORDER BY name ASC', 'm_mail_contents[mails.mail_id][1] as contents')
       ->setReadPermission('mails', 'admin')->setDefault('mails', '*')->addConstraint('contents', ['enabled', '=', 'true']);
$router->post('/admin/mails', null, 'admin', 'm_mails as mails', 'm_mail_contents as contents')
       ->setAllPermissions('mails', 'admin')->setAllPermissions('contents', 'admin')->setDeleteCascade('mails', 'contents');

$router->get('/admin/mails/edit/{mail_id}', 'Admin/Mails/Edit', 'admin', 'm_mails[mail_id][1] as mails', 'm_mail_contents[mails.mail_id][9] as contents', 'm_mail_stats[contents.mail_content_id] as stats')
       ->setDefault('mail_id', '0')->setReadPermission('mails', 'admin');
$router->post('/admin/mails/edit/{mail_id}', null, 'admin', 'm_mails as mails', 'm_mail_contents as contents')
       ->setDefault('mail_id', '0')->setAllPermissions('mails', 'admin')->setAllPermissions('contents', 'admin');

$router->get('/admin/gmail/setup', null, 'admin', 'm_configs[type] as configs')
       ->setReadPermission('configs', 'admin')->setDefault('type', 'google');
$router->post('/admin/gmail/setup', null, 'admin', 'm_configs as configs')
       ->setAllPermissions('configs', 'admin');

$router->get('/_mail/open', 'Mail/Open', false)->setDefault('_noView', true);

$router->get('/admin/gmail/start', 'Admin/Gmail@start', 'admin')->setDefault('_noView', true);
$router->get('/admin/gmail/authorize', 'Admin/Gmail@auth', 'admin')->setDefault('_noView', true);

$router->get('/members/subscriptions', 'Members/Profile/Subscriptions', true, 'm_mail_unsubscribes[99] as unsubscribes')
       ->setReadPermission('unsubscribes', Permission::SAME_USER);
$router->post('/members/subscriptions', null, true, 'm_mail_unsubscribes as unsubscribes')
       ->setAllPermissions('unsubscribes', Permission::SAME_USER);
