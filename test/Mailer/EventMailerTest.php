
<?php

namespace Test\Mailer {

    use Auryn\Injector;
    use Minute\Event\UserMailEvent;
    use Minute\Mail\EventMailer;
    use Mockery as m;
    use Mockery\Adapter\Phpunit\MockeryTestCase;

    class EventMailerTest extends MockeryTestCase {
        public function testSendMail() {
            $userMock = m::mock('alias:App\Model\User', ['attributes' => ['first_name' => 'San', 'email' => 'san@localhost']]);;
            $userMock->first_name = 'San';
            $userMock->email      = 'san@localhost';
            $userMock->user_id    = 8;

            $mailMock = m::mock('alias:App\Model\Mail');;
            $mailMock->mail_type = 'tip';

            $contentMock = m::mock('alias:App\Model\Content');;
            $contentMock->subject         = 'Hi {first_name}!';
            $contentMock->html            = '<p><a href="{auth}/members?go={video.link}">link</a></p><p><img alt="click here" src=\'http://san/clicky.gif\'></p><p>{signature}</p>';
            $contentMock->attachment      = __DIR__ . '/data/blank.gif';
            $contentMock->mail_content_id = 8;
            $contentMock->track_opens     = 'true';
            $contentMock->track_clicks    = 'true';
            $contentMock->embed_images    = 'true';

            $mailInfoMock = m::mock('Minute\Mail\MailInfo');
            $mailInfoMock->shouldReceive('getMail')->andReturn($mailMock);
            $mailInfoMock->shouldReceive('getMailContent')->andReturn($contentMock);

            $unsubscribeMock = m::mock('Minute\Mail\Unsubscribe');
            $unsubscribeMock->shouldReceive('isUnsubscribed')->andReturn(false);

            $trackerMock = m::mock('Minute\Track\UserTracker');
            $trackerMock->shouldReceive('createAuthUrl')->andReturn('auth_url');
            $trackerMock->shouldReceive('createTrackingUrl')->andReturn('tracking_url');

            /** @var UserMailEvent $userMailEvent */
            $userMailEventMock = m::mock('\Minute\Event\UserMailEvent');
            $userMailEventMock->shouldReceive('getUser')->andReturn($userMock);
            $userMailEventMock->shouldReceive('getTemplate')->andReturn('mockTemplate');
            $userMailEventMock->shouldReceive('getReplacements')->andReturn(['replyTo' => ['foo@bar' => 'Foo'], 'video' => ['link' => '/myvideo']]);
            $userMailEventMock->shouldReceive('setHandled')->with(false)->andReturn(false);

            $browserMock = m::mock('Minute\Http\Browser');
            $browserMock->shouldReceive('downloadCached')->andReturn($contentMock->attachment);

            /** @var EventMailer $eventMailer */
            $eventMailer = (new Injector())->make(
                'Minute\Mail\EventMailer',
                [':mailInfo' => $mailInfoMock, ':unsubscribe' => $unsubscribeMock, ':tracker' => $trackerMock, ':downloader' => $browserMock]
            );
            
            $message     = $eventMailer->sendMail($userMailEventMock);

            $this->assertContains('@', array_keys($message->getFrom())[0], 'From: of email is set');
            $this->assertEquals(['san@localhost' => 'San'], $message->getTo(), 'To: of email is set');
            $this->assertEquals('Hi San!', $message->getSubject(), 'Subject: of email is set');
            $this->assertContains('<p><a href="auth_url/members?go=/myvideo">link</a></p>', $message->getBody(), 'Message body tags were replaced');
            $this->assertContains('<p><img src="tracking_url/mailer/open" width="1" height="1"></p>', $message->getBody(), 'Tracking image was added');
            $this->assertContains('<img alt="click here" src=\'cid:', $message->getBody(), 'Image was converted to inline');
            $this->assertContains('Message-ID:', $message->toString(), 'Message-ID: of email is set');
            $this->assertContains('Content-Type: text/plain', $message->toString(), 'There is a plain text version');
            $this->assertContains('Content-Type: text/html', $message->toString(), 'There is a HTML version');
            $this->assertContains('Reply-To: Foo <foo@bar>', $message->toString(), 'Reply-To: header is set');
        }
    }
}