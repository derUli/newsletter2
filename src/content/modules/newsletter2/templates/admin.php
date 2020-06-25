<?php if (Request::getVar("message")) { ?>
    <div class="alert alert-success">
        <?php translate(Request::getVar("message")); ?>
    </div>
<?php } ?>
<?php
echo Template::executeModuleTemplate("newsletter2", "menu.php");

