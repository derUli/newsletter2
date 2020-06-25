<?php

namespace Newsletter;

use Database;

class SubscriberList {

    public function getAllSubscribers() {
        $subscribers = array();
        $query = Database::pQuery("select id from `{prefix}newsletter_subscribers` order by email", array(), true);
        while ($row = Database::fetchObject($query)) {
            $subscribers[] = new Subscriber($row->id);
        }
        return $subscribers;
    }

    public function getAllConfirmedSubscribers() {
        $subscribers = array();
        $query = Database::pQuery("select id from `{prefix}newsletter_subscribers` where confirmed = ? order by email", array(
                    1
                        ), true);
        while ($row = Database::fetchObject($query)) {
            $subscribers[] = new Subscriber($row->id);
        }
        return $subscribers;
    }

    public function getAllNotConfirmedSubscribers() {
        $subscribers = array();
        $query = Database::pQuery("select id from `{prefix}newsletter_subscribers` where confirmed = ? order by email", array(
                    0
                        ), true);
        while ($row = Database::fetchObject($query)) {
            $subscribers[] = new Subscriber($row->id);
        }
        return $subscribers;
    }

}
