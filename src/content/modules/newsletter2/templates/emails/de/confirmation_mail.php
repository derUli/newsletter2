<!DOCTYPE html>
<html>
    <head>
        <base href="<?php esc(ViewBag::get("base_url")); ?>"/>
        <meta charset="UTF-8">
        <title>Newsletter bestätigen</title>
    </head>
    <body>
        <p>Vielen Dank für das Abonnieren, des E-Mail Newsletters von "<?php echo ViewBag::get("homepage_title"); ?>"!</p>

        <p>
            Bitte klicken Sie auf folgenden Link, um den Empfang des Newsletters zu bestätigen:<br/>
            <a href="<?php echo ViewBag::get("confirmation_link"); ?>">Newsletter bestätigen</a>	
        </p>

        <p>Sollten Sie diese E-Mail ungewünscht empfangen haben, ignorieren Sie diese bitte einfach.</p>
    </body>
</html>
