<?php

namespace Newsletter;

use Mailer;
use Settings;

class Newsletter {

    const FORMAT_HTML = "html";
    const FORMAT_TEXT = "text";

    private $receivers = array();
    private $title;
    private $body;
    private $unsubscribe_url;
    private $format = self::FORMAT_HTML;

    public function getReceivers() {
        return $this->receivers;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getBody() {
        return $this->body;
    }

    public function getFormat() {
        return $this->format;
    }

    public function setReceivers($val) {
        $this->receivers = is_array($val) ? $val : null;
    }

    public function setTitle($val) {
        $this->title = !is_null($val) ? strval($val) : null;
    }

    public function setBody($val) {
        $this->body = !is_null($val) ? strval($val) : null;
    }

    public function setFormat($val) {
        $this->format = $val;
    }

    public function setUnsubscribeUrl($val) {
        $this->unsubscribe_url = $val;
    }

    public function getUnsubscribeUrl($email = null, $code = null) {
        $url = $this->unsubscribe_url;
        if (!is_null($email)) {
            $url = str_ireplace("_email", $email, $url);
        }
        if (!is_null($code)) {
            $url = str_ireplace("_code", $code, $url);
        }
        return $url;
    }

    public function send() {
        $headers = "From: " . Settings::get("email") . "\r\n";
        $contentType = $this->format == self::FORMAT_HTML ? "text/html" : "text/plain";
        $headers .= "Content-Type: {$contentType}; charset=utf-8";

        foreach ($this->getReceivers() as $recipient) {
            $body = $this->body;
            $body = str_ireplace("%unsubscribe_link%", $this->getUnsubscribeUrl($recipient->getEmail(), $recipient->getConfirmationCode()), $body);

            if (class_exists('\MailQueue\MailQueue')) {
                $queue = \MailQueue\MailQueue::getInstance();
                $mail = new \MailQueue\Mail();
                $mail->setRecipient($recipient->getEmail());
                $mail->setHeaders($headers);
                $mail->setSubject($this->title);
                $mail->setMessage($body);
                $queue->addMail($mail);
            } else {
                Mailer::send($recipient->getEmail(), $this->title, $body, $headers);
            }
        }
    }

}
