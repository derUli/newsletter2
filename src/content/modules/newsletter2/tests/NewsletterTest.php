<?php

use PHPUnit\Framework\TestCase;
use Newsletter\SubscriberList;
use Newsletter\Subscriber;
use Newsletter\Newsletter;

class NewsletterTest extends TestCase {

    protected function setUp(): void {
        $migrator = new DBMigrator(
                "module/newsletter2",
                ModuleHelper::buildRessourcePath("newsletter2", "sql/up")#
        );
        $migrator->migrate();
    }

    protected function tearDown(): void {
        $migrator = new DBMigrator("module/newsletter2", ModuleHelper::buildRessourcePath("newsletter2", "sql/down"));
        $migrator->rollback();
    }

    public function testCreateEditAndDeleteSubscriber() {
        $subscriber = new Subscriber();
        $subscriber->setEmail("john@doe.de");
        $subscriber->setConfirmed(1);
        $timestamp = time();
        $subscriber->setSubscriptionDate($timestamp);
        $subscriber->save();
        $this->assertNotNull($subscriber->getID());
        $id = $subscriber->getID();

        $subscriber = new Subscriber();
        $subscriber->loadByEmail("john@doe.de");
        $this->assertNotNull($subscriber->getID());
        $this->assertEquals("john@doe.de", $subscriber->getEmail());
        $this->assertTrue($subscriber->getConfirmed());
        $this->assertEquals($timestamp, $subscriber->getSubscribeDate());

        $subscriber = new Subscriber($id);
        $this->assertNotNull($subscriber->getID());
        $this->assertEquals("john@doe.de", $subscriber->getEmail());
        $this->assertTrue($subscriber->getConfirmed());
        $this->assertEquals($timestamp, $subscriber->getSubscribeDate());

        $subscriber->setEmail("foo@bar.de");
        $subscriber->setConfirmed(0);
        $newTime = time() + 500;
        $subscriber->setSubscriptionDate($newTime);
        $subscriber->save();

        $subscriber = new Subscriber($id);
        $this->assertNotNull($subscriber->getID());
        $this->assertEquals("foo@bar.de", $subscriber->getEmail());
        $this->assertFalse($subscriber->getConfirmed());
        $this->assertEquals($newTime, $subscriber->getSubscribeDate());

        $this->assertFalse($subscriber->confirm("invalid_code"));
        $this->assertTrue($subscriber->confirm($subscriber->getConfirmationCode()));
        $subscriber = new Subscriber($id);
        $this->assertTrue($subscriber->getConfirmed());

        $subscriber->delete();

        $subscriber = new Subscriber($id);
        $this->assertNull($subscriber->getID());
    }

    public function testNewsletterListFunctions() {
        $list = new SubscriberList();
        for ($i = 1; $i <= 5; $i++) {
            $subscriber = new Subscriber();
            $subscriber->setEmail("testuser{$i}@mail.de");
            $subscriber->setConfirmed(1);
            $subscriber->setSubscriptionDate(time());
            $subscriber->save();
        }
        for ($i = 6; $i <= 8; $i++) {
            $subscriber = new Subscriber();
            $subscriber->setEmail("testuser{$i}@mail.de");
            $subscriber->setConfirmed(0);
            $subscriber->setSubscriptionDate(time());
            $subscriber->save();
        }
        $this->assertCount(8, $list->getAllSubscribers());
        $this->assertCount(5, $list->getAllConfirmedSubscribers());
        $this->assertCount(3, $list->getAllNotConfirmedSubscribers());
    }

    public function testSendNewsletter() {
        for ($i = 1; $i <= 10; $i++) {
            $subscriber = new Subscriber();
            $subscriber->setEmail("testuser{$i}@mail.de");
            $subscriber->setConfirmed(1);
            $subscriber->setSubscriptionDate(time());
            $subscriber->save();
        }
        $list = new SubscriberList();
        $newsletter = new Newsletter();
        $newsletter->setFormat(Newsletter::FORMAT_HTML);
        $newsletter->setTitle("My Newsletter");
        $newsletter->setReceivers($list->getAllConfirmedSubscribers());
        $newsletter->setBody("<h1>Hello World</h1>");
        $this->assertEquals(Newsletter::FORMAT_HTML, $newsletter->getFormat());
        $this->assertEquals("My Newsletter", $newsletter->getTitle());
        $this->assertEquals("<h1>Hello World</h1>", $newsletter->getBody());
        $this->assertCount(10, $list->getAllConfirmedSubscribers());
        $newsletter->send();
    }

}
