<!DOCTYPE html>
<html>
    <head>
        <base href="<?php esc(ViewBag::get("base_url")); ?>"/>
        <meta charset="UTF-8">
        <title>Confirm Newsletter Subscription</title>
    </head>
    <body>
        <p>Thank you for subscribing the newsletter of "<?php echo ViewBag::get("homepage_title"); ?>"!</p>

        <p>
            Plese click the following link to confirm the subscription:<br/>
            <a href="<?php echo ViewBag::get("confirmation_link"); ?>">Confirm Newsletter Subscription</a>
        </p>

        <p>If you haven't requested this mail, please just ignore it.</p>
    </body>
</html>