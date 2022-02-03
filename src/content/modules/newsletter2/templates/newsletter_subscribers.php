<?php
$acl = new ACL();

use Newsletter\SubscriberList;

$subscribers_filter_confirmed = isset($_SESSION["subscribers_filter_confirmed"]) ? $_SESSION["subscribers_filter_confirmed"] : "all";

$subscribers_filter_email = isset($_SESSION["subscribers_filter_email"]) ? $_SESSION["subscribers_filter_email"] : "";
$subscribers_filter_email = trim($subscribers_filter_email);

$list = new SubscriberList();

switch ($subscribers_filter_confirmed) {
    case "yes":
        $subscriberList = $list->getAllConfirmedSubscribers();
        break;
    case "no":
        $subscriberList = $list->getAllNotConfirmedSubscribers();
        break;
    default:
        $subscriberList = $list->getAllSubscribers();
        break;
}

$subscribers = [];

if (StringHelper::isNullOrWhitespace($subscribers_filter_email)) {
    $subscribers = $subscriberList;
} else {
    foreach ($subscriberList as $subscriber) {
        if (startsWith(strtolower($subscriber->getEmail()), strtolower($subscribers_filter_email))) {
            $subscribers[] = $subscriber;
        }
    }
}
?>
<h1><?php translate("subscribers") ?></h1>
<div class="form-group">
    <a href="<?php echo ModuleHelper::buildAdminURL("newsletter2"); ?>"
       class="btn btn-default btn-back"><?php echo UliCMS\HTML\icon("fa fa-arrow-left"); ?> 
        <?php translate("back"); ?></a> 
    <?php if ($acl->hasPermission("newsletter_subscribers_add")) { ?>
        <a
            href="<?php echo ModuleHelper::buildActionURL("newsletter_subscribers_add"); ?>"
            class="btn btn-primary pull-right"><?php echo UliCMS\HTML\icon("fa fa-plus"); ?> 
            <?php translate("add"); ?></a>
    <?php } ?>
</div>

<?php echo ModuleHelper::buildMethodCallForm("NewsletterController", "filterSubscribers"); ?>
<div class="form-group">
    <label for="confirmed"><?php translate("confirmed"); ?></label> <select
        name="confirmed" id="confirmed">
        <option value="all"
                <?php if ($subscribers_filter_confirmed == "all") echo "selected"; ?>><?php translate("all"); ?></option>
        <option value="yes"
                <?php if ($subscribers_filter_confirmed == "yes") echo "selected"; ?>><?php translate("yes"); ?></option>
        <option value="no"
                <?php if ($subscribers_filter_confirmed == "no") echo "selected"; ?>><?php translate("no"); ?></option>
    </select>
</div>

<div class="form-group">
    <label for="email"><?php translate("email"); ?></label> <input
        type="text" name="email"
        value="<?php esc($subscribers_filter_email); ?>">
</div>

<div class="form-group">
    <button type="submit" class="btn btn-default">
        <?php echo UliCMS\HTML\icon("fa fa-search"); ?> 
        <?php translate("search"); ?></button>
</div>
<?php echo ModuleHelper::endForm(); ?>

<?php echo ModuleHelper::buildMethodCallForm("NewsletterController", "subscriberAction"); ?>
<div class="scroll">
    <table class="tablesorter">
        <thead>
            <tr>
                <?php if ($acl->hasPermission("newsletter_subscribers_change")) { ?>
                    <td
                        style="width: 30px;"
                        class="nosort"><input type="checkbox" class="checkbox"
                                          id="check-all" value="1"></td>
                    <?php } ?>
                <th><?php translate("email"); ?></th>
                <th><?php translate("date"); ?></th>
                <th><?php translate("confirmed"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subscribers as $subscriber) { ?>
                <tr>
                    <?php if ($acl->hasPermission("newsletter_subscribers_change")) { ?>
                        <td>
                            <input type="checkbox"
                                   class="subscriber-checkbox checkbox"
                                   name="subscribers[]"
                                   value="<?php echo $subscriber->getID(); ?>">
                        </td>
                    <?php } ?>
                    <td><?php esc($subscriber->getEmail()); ?></td>
                    <td><?php esc(PHP81_BC\strftime("%Y-%m-%d %H:%M:%S", $subscriber->getSubscribeDate())); ?></td>
                    <td><?php esc(bool2YesNo($subscriber->getConfirmed())); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php if ($acl->hasPermission("newsletter_subscribers_change")) { ?>
    <div class="row voffset2">
        <div class="col-xs-8">
            <select name="action">
                <option selected><?php translate("please_select"); ?></option>
                <option value="confirm"><?php translate("confirm"); ?></option>
                <option value="delete"><?php translate("delete"); ?></option>
            </select>
        </div>
        <div class="col-xs-4 text-right">
            <button type="submit" class="btn btn-default">
                <?php echo UliCMS\HTML\icon("fas fa-running"); ?> 
                <?php translate("do_action"); ?>
            </button>
        </div>
    </div>
<?php } ?>
<?php echo ModuleHelper::endForm(); ?>

<?php
enqueueScriptFile(ModuleHelper::buildRessourcePath("newsletter2", "js/newsletter_subscribers.js"));
combinedScriptHtml();
?>