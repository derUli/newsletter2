<h1><?php translate("add_subscribers")?></h1>
<div class="form-group">
	<a
		href="<?php echo ModuleHelper::buildActionURL("newsletter_subscribers");?>"
		class="btn btn-default btn-back"><?php translate("back");?></a>
</div>
<?php echo ModuleHelper::buildMethodCallForm("NewsletterController", "addSubscribers");?>
<div class="form-group">
<label for="subscribers"><?php translate("subscribers");?></label>
<textarea name="subscribers" id="subscribers" rows="8"></textarea>
</div>
<button type="submit" class="btn btn-primary"><?php translate("add_subscribers");?></button>
<?php echo ModuleHelper::endForm();?>