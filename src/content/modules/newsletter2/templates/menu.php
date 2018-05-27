<?php $acl = new ACL();?>
<div class="btn-group-vertical">
<?php if($acl->hasPermission("newsletter_write")){?>
	<div>
		<a
			href="<?php echo ModuleHelper::buildActionURL("write_newsletter");?>"
			class="btn btn-primary"><?php translate("write_newsletter");?></a>
	</div>
<?php }?>
<?php if($acl->hasPermission("newsletter_subscribers_list")){?>
	<div class="voffset3">
		<a href="<?php echo ModuleHelper::buildActionURL("newsletter_subscribers");?>" class="btn btn-default"><?php translate("show_subscribers");?></a>
	</div>
	<?php }?>
<?php if($acl->hasPermission("newsletter_edit_template")){?>
	<div class="voffset3">
		<a
			href="<?php echo ModuleHelper::buildActionURL("edit_newsletter_template");?>"
			class="btn btn-danger"><?php translate("edit_template");?></a>
	</div>
	<?php }?>
</div>