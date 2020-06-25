<!DOCTYPE html>
<html>
    <head>
        <base href="<?php esc(ViewBag::get("base_url")); ?>"/>
        <meta charset="UTF-8">
        <title><?php esc(ViewBag::get("title")); ?></title>
    </head>
    <body>
        <?php echo ViewBag::get("body"); ?>
    </body>
</html>