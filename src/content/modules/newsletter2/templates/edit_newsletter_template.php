<?php $user = new User();
$user->loadById(get_user_id());

?>
<h1><?php translate("edit_template");?></h1>
<?php if(Request::getVar("save")){
    ?>
<div class="alert alert-success alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<?php translate("changes_was_saved")?>
		</div>
		<?php }?>
<div class="form-group">
	<a href="<?php echo ModuleHelper::buildAdminURL("newsletter2");?>"
		class="btn btn-default btn-back"><?php translate("back");?></a>
</div>
<?php echo ModuleHelper::buildMethodCallForm("NewsletterController", "saveTemplate");?>
  <div class="form-group">
      <label for="newsletter_template_title"><?php translate("title")?></label>
      <input type="text" name="newsletter_template_title" id="newsletter_template_title" value="<?php esc(Settings::get("newsletter_template_title"));?>">
  </div>
  <div class="form-group">
      <label for="body"><?php translate("template")?></label>
      <textarea class="<?php esc($user->getHTMLEditor()); ?>" data-mimetype="text/html" name="newsletter_template_content" id="newsletter_template_content"><?php esc(Settings::get("newsletter_template_content"));?></textarea>
      </div>
      <button type="submit" class="btn btn-primary"><?php translate("save")?></button>
<?php echo ModuleHelper::endForm();?>

