{*
$Id: jquery_loadmask_js.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{load_defer file="lib/jquery.loadmask.js" type="js"}
{capture name=loadmask}
var lbl_loading = '{$lng.lbl_loading|wm_remove|escape:"javascript"}';
{/capture}
{load_defer file="loadmask" direct_info=$smarty.capture.loadmask type="js"}
