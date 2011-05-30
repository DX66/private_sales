<?php
/* vim: set ts=4 sw=4 sts=4 et: */
require './top.inc.php';
require './init.php';

$result = $smarty->clear_all_cache();
$result = $smarty->clear_compiled_tpl();
$result = func_rm_dir($var_dirs['templates_c'], true);
$result = func_rm_dir($var_dirs['cache'], true);
?>
The compiled templates cache ('var/templates_c' directory) has been cleaned up.<br />
The X-Cart cache ('var/cache' directory) has been cleaned up.
<?php if ($result['is_large']) { ?>
<br /><b>Note:</b> some files were not removed. Please, delete them manually.
<?php } ?>

