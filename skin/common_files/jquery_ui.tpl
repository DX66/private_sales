{*
$Id: jquery_ui.tpl,v 1.4 2010/06/25 06:33:58 igoryan Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{load_defer file="lib/jqueryui/jquery.ui.core.min.js" type="js"}
{load_defer file="lib/jqueryui/jquery.ui.widget.min.js" type="js"}
{load_defer file="lib/jqueryui/jquery.ui.position.min.js" type="js"}
{load_defer file="lib/jqueryui/jquery.ui.mouse.min.js" type="js"}
{load_defer file="lib/jqueryui/jquery.ui.button.min.js" type="js"}
{load_defer file="lib/jqueryui/jquery.ui.dialog.min.js" type="js"}
{load_defer file="lib/jqueryui/jquery.ui.resizable.min.js" type="js"}
{load_defer file="lib/jqueryui/jquery.ui.draggable.min.js" type="js"}
{load_defer file="lib/jqueryui/jquery.ui.tabs.min.js" type="js"}
{load_defer file="lib/jqueryui/jquery.ui.datepicker.min.js" type="js"}

{if $usertype eq 'C'}
  {load_defer file="lib/jqueryui/jquery.ui.theme.css" type="css"}
{else}
  {load_defer file="lib/jqueryui/jquery.ui.admin.css" type="css"}
{/if}
