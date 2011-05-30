{*
$Id: show_status.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $condition}
<font color="green">{$label_true}</font>
{else}
<font color="red">{$label_false}</font>
{/if}
