<?php
echo ModuleHelper::buildMethodCallForm("NewsletterController", "subscribe", [], "post", [
    "class" => "newsletter-form"
]);
?>
<?php if(Request::getVar("message")){?>
<div class="alert alert-success">
<?php translate(Request::getVar("message"));?>
</div>
<?php }?>

<div class="form-group">
	<label for="email"><?php translate("your_email_address")?></label> <input
		type="email" required class="form-control" name="email" id="email">
</div>
<?php $checkbox = new PrivacyCheckbox(getCurrentLanguage(true));?>
<?php

if ($checkbox->isEnabled()) {
    ?>
<div class="privacy-checkbox">
	<?php
    echo $checkbox->render();
    ?></div><?php
}
?>
	<?php csrf_token_html();?>

<button type="submit" class="btn btn-primary"><?php translate("subscribe_newsletter");?></button>
<?php echo ModuleHelper::endForm();?>