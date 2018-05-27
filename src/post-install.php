<?php
Settings::register ( "newsletter_template_title", "Titel" );
Settings::register ( "newsletter_template_content", "<p>Fügen Sie hier Ihren Text ein.</p>" );
Settings::register ( "newsletter_id", "1" );
	
$migrator = new DBMigrator("module/newsletter2", ModuleHelper::buildRessourcePath("newsletter2", "sql/up"));
$migrator->migrate();