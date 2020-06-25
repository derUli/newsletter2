<?php
$user = new User();
$user->loadById(get_user_id());

$sendTo = array();
$sendTo[Settings::get("email")] = Settings::get("email");
if (Settings::get("email") != $user->getEmail()) {
    $sendTo[$user->getEmail()] = $user->getEmail();
}

$sendTo["all"] = get_translation("all_subscribers");

$title = isset($_SESSION["newsletter_title"]) ? $_SESSION["newsletter_title"] : Settings::get("newsletter_template_title");
$content = isset($_SESSION["newsletter_content"]) ? $_SESSION["newsletter_content"] : Settings::get("newsletter_template_content");
?>
<h1><?php translate("write_newsletter"); ?></h1>
<div class="form-group">
    <a href="<?php echo ModuleHelper::buildAdminURL("newsletter2"); ?>"
       class="btn btn-default btn-back"><?php echo UliCMS\HTML\icon("fa fa-arrow-left"); ?> <?php translate("back"); ?></a>
</div>
<?php echo ModuleHelper::buildMethodCallForm("NewsletterController", "sendNewsletter"); ?>
<div class="form-group">
    <label for="title"><?php translate("title") ?></label> <input
        type="text" name="title" id="title" value="<?php esc($title); ?>">
</div>
<div class="form-group">
    <label for="body"><?php translate("mail_text") ?></label>
    <textarea class="<?php esc($user->getHTMLEditor()); ?>"
              data-mimetype="text/html" name="body" id="body"><?php esc($content); ?></textarea>
</div>

<div class="form-group">
    <label for="send_to"><?php translate("send_to") ?></label> <select
        name="send_to" id="send_to">
            <?php foreach ($sendTo as $value => $title) { ?>
            <option value="<?php esc($value); ?>"><?php esc($title); ?></option>
        <?php } ?>
    </select>
</div>

<button type="submit" class="btn btn-primary">
    <?php echo UliCMS\HTML\icon("fas fa-mail-bulk"); ?>  
    <?php translate("send_newsletter") ?></button>
<?php echo ModuleHelper::endForm(); ?>