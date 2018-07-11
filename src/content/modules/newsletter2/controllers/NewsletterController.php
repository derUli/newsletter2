<?php
use Newsletter\Subscriber;
use Newsletter\SubscriberList;
use Newsletter\Newsletter;

class NewsletterController extends Controller
{

    private $moduleName = "newsletter2";

    public function render()
    {
        return Template::executeModuleTemplate($this->moduleName, "subscribe.php");
    }

    public function settings()
    {
        return Template::executeModuleTemplate($this->moduleName, "admin.php");
    }

    public function getSettingsHeadline()
    {
        return get_translation("newsletter");
    }

    public function getSettingsLinkText()
    {
        return get_translation("open");
    }

    public function sendNewsletterPost()
    {
        $title = Request::getVar("title");
        $body = Request::getVar("body");
        
        $_SESSION["newsletter_title"] = $title;
        $_SESSION["newsletter_content"] = $body;
        
        $mail_to = Request::getVar("send_to");
        $receivers = array();
        $user = new User();
        $user->loadById(get_user_id());
        $userEmail = $user->getEmail();
        if ($mail_to == "all") {
            $list = new SubscriberList();
            $receivers = $list->getAllConfirmedSubscribers();
        } else if (in_array($mail_to, array(
            $userEmail,
            getconfig("email")
        ))) {
            $subscriber = new Subscriber();
            $subscriber->setEmail($mail_to);
            $subscriber->setSubscriptionDate(time());
            $receivers[] = $subscriber;
        }
        if (count($receivers) > 0) {
            // TODO:
            // * prepend domain and protocol before site relative urls
            
            $date = date(Settings::get("date_format"));
            
            $title = str_ireplace("%newsletter_id%", Settings::get("newsletter_id"), $title);
            $title = str_ireplace("%year%", strftime("%Y"), $title);
            $title = str_ireplace("%month%", utf8_encode(strftime("%B")), $title);
            $title = str_ireplace("%date%", $date, $title);
            
            $body = str_ireplace("%newsletter_id%", Settings::get("newsletter_id"), $body);
            $body = str_ireplace("%year%", strftime("%Y"), $body);
            $body = str_ireplace("%month%", utf8_encode(strftime("%B")), $body);
            $body = str_ireplace("%date%", $date, $body);
            $body = str_ireplace("%title%", $title, $body);
            
            $body = function_exists("absolutify") ? absolutify($body, ModuleHelper::getBaseUrl()) : $body;
            
            ViewBag::set("base_url", getBaseFolderURL());
            ViewBag::set("title", $title);
            ViewBag::set("body", $body);
            
            $renderedBody = Template::executeModuleTemplate($this->moduleName, "newsletter_base.php");
            
            $newsletter = new Newsletter();
            $newsletter->setTitle($title);
            $newsletter->setFormat(Newsletter::FORMAT_HTML);
            $newsletter->setBody($renderedBody);
            $newsletter->setReceivers($receivers);
            
            $page = ModuleHelper::getFirstPageWithModule($this->moduleName);
            $id = $page->id;
            $url = ModuleHelper::getFullPageURLByID($id);
            $url .= str_contains("?", $url) ? "&" : "?";
            $url .= ModuleHelper::buildQueryString(array(
                "sClass" => "NewsletterController",
                "sMethod" => "unsubscribe",
                "email" => "_email",
                "code" => "_code"
            ), false);
            
            $newsletter->setUnsubscribeUrl($url);
            
            $newsletter->send();
            
            // increase newsletter number count
            Settings::set("newsletter_id", intval(Settings::get("newsletter_id")) + 1);
            
            Request::redirect(ModuleHelper::buildAdminURL("newsletter2", "message=newsletter_sent"));
        } else {
            ExceptionResult(get_translation("no_recipients_specified"), HttpStatusCode::BAD_REQUEST);
        }
    }

    public function saveTemplatePost()
    {
        $newsletter_template_title = Request::getVar("newsletter_template_title");
        $newsletter_template_content = Request::getVar("newsletter_template_content");
        
        Settings::set("newsletter_template_title", $newsletter_template_title);
        Settings::set("newsletter_template_content", $newsletter_template_content);
        Request::redirect(ModuleHelper::buildActionURL("edit_newsletter_template", "save=1"));
    }

    public function confirm()
    {
        $email = Request::getVar("email");
        $code = Request::getVar("code");
        if (! $email or ! $code) {
            ExceptionResult(get_translation("fill_all_fields"), HttpStatusCode::BAD_REQUEST);
        }
        $subscriber = new Subscriber();
        $subscriber->loadByEmail($email);
        if (! $subscriber->getID()) {
            ExceptionResult(get_translation("no_such_email_in_database"), HttpStatusCode::NOT_FOUND);
        }
        if (! $subscriber->getConfirmed() && $subscriber->confirm($code)) {
            $page = ModuleHelper::getFirstPageWithModule($this->moduleName);
            $id = $page->id;
            $url = ModuleHelper::getFullPageURLByID($id);
            $url .= str_contains("?", $url) ? "&" : "?";
            $url .= "message=newsletter_confirmed";
            Request::redirect($url);
        } else {
            ExceptionResult(get_translation("invalid_token"), HttpStatusCode::NOT_FOUND);
        }
    }

    public function subscribePost()
    {
        $email = Request::getVar("email");
        $checkbox = new PrivacyCheckbox(getCurrentLanguage(true));
        if (StringHelper::isNullOrWhitespace($email) or ! str_contains("@", $email) or ($checkbox->isEnabled() and ! $checkbox->isChecked())) {
            ExceptionResult(get_translation("fill_all_fields"), HttpStatusCode::BAD_REQUEST);
        }
        $subscriber = new Subscriber();
        $subscriber->loadByEmail($email);
        if ($subscriber->getID()) {
            ExceptionResult(get_translation("already_subscribed"));
        }
        $subscriber->setEmail($email);
        $subscriber->setSubscriptionDate(time());
        $subscriber->setConfirmed(false);
        $subscriber->save();
        $homepage_title = Settings::get("homepage_title_" . $_SESSION["language"]);
        if (! $homepage_title) {
            $homepage_title = Settings::get("homepage_title");
        }
        $url = get_referrer();
        $parsedUri = parse_url($url);
        $url = $parsedUri["scheme"] . "://" . $parsedUri["host"] . $parsedUri["path"];
        
        $confirmationUrl = getBaseFolderURL() . "/" . ModuleHelper::buildMethodCallUrl("NewsletterController", "confirm", "code=" . urlencode($subscriber->getConfirmationCode()) . "&email=" . urlencode($email));
        
        ViewBag::set("homepage_title", $homepage_title);
        ViewBag::set("confirmation_link", $confirmationUrl);
        
        $headers = "From: " . Settings::get("email") . "\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8";
        
        $language = getCurrentLanguage();
        
        $subject = get_translation("newsletter_confirmation_subject");
        
        $mailBody = Template::executeModuleTemplate($this->moduleName, "emails/{$language}/confirmation_mail.php");
        
        // TODO: error handling if mail send fails
        if (class_exists('\MailQueue\MailQueue')) {
            $queue = \MailQueue\MailQueue::getInstance();
            $mail = new \MailQueue\Mail();
            $mail->setRecipient($subscriber->getEmail());
            $mail->setHeaders($headers);
            $mail->setSubject($subject);
            $mail->setMessage($mailBody);
            $queue->addMail($mail);
        } else {
            Mailer::send($email, $subject, $mailBody, $headers);
        }
        
        $redirectUrl = $url . "?message=please_confirm_newsletter";
        Request::redirect($redirectUrl);
    }

    public function unsubscribe()
    {
        $email = Request::getVar("email");
        $code = Request::getVar("code");
        if (! $email or ! $code) {
            ExceptionResult(get_translation("fill_all_fields"), HttpStatusCode::BAD_REQUEST);
        }
        $subscriber = new Subscriber();
        $subscriber->loadByEmail($email);
        if (! $subscriber->getID()) {
            ExceptionResult(get_translation("no_such_email_in_database"), HttpStatusCode::NOT_FOUND);
        }
        if ($subscriber->getConfirmationCode() != $code) {
            ExceptionResult(get_translation("invalid_token"), HttpStatusCode::NOT_FOUND);
        }
        $subscriber->delete();
        $page = ModuleHelper::getFirstPageWithModule($this->moduleName);
        $id = $page->id;
        $url = ModuleHelper::getFullPageURLByID($id);
        $url .= str_contains("?", $url) ? "&" : "?";
        $url .= "message=newsletter_unsubscribed";
        Request::redirect($url);
    }

    public function cron()
    {
        // delete unconfirmed subscriber addresses after 2 days
        $confirmTimeout = 60 * 60 * 24 * 2;
        Database::pQuery("DELETE FROM `{prefix}newsletter_subscribers` WHERE ? - `subscribe_date` > ? AND confirmed = ?", array(
            time(),
            $confirmTimeout,
            0
        ), true);
    }

    public function filterSubscribersPost()
    {
        $_SESSION["subscribers_filter_confirmed"] = Request::getVar("confirmed");
        $_SESSION["subscribers_filter_email"] = Request::getVar("email");
        Request::redirect(ModuleHelper::buildActionURL("newsletter_subscribers"));
    }

    public function subscriberActionPost()
    {
        $action = Request::getVar("action");
        $subscribers = Request::getVar("subscribers");
        foreach ($subscribers as $subscriber) {
            $subscriber = new Subscriber($subscriber);
            switch ($action) {
                case "confirm":
                    $subscriber->setConfirmed(1);
                    $subscriber->save();
                    break;
                case "delete":
                    $subscriber->delete();
                    break;
            }
        }
        Request::redirect(ModuleHelper::buildActionURL("newsletter_subscribers"));
    }

    public function addSubscribersPost()
    {
        $subscribers = Request::getVar("subscribers");
        $subscribers = StringHelper::linesFromString($subscribers, true, true, true);
        foreach ($subscribers as $email) {
            if (! str_contains("@", $email)) {
                continue;
            }
            $subscriber = new Subscriber();
            $subscriber->loadByEmail($email);
            if ($subscriber->getID()) {
                continue;
            }
            $subscriber->setEmail($email);
            $subscriber->setSubscriptionDate(time());
            $subscriber->setConfirmed(true);
            $subscriber->save();
        }
        Request::redirect(ModuleHelper::buildActionURL("newsletter_subscribers"));
    }

    public function uninstall()
    {}
}