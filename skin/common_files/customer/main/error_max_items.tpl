{*
$Id: error_max_items.tpl,v 1.1 2010/05/21 08:32:04 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_warning}</h1>

<div class="error-message text-block">{$lng.err_checkout_max_items_msg|substitute:"quantity":$config.General.maximum_order_items}</div>
{include file="customer/buttons/go_back.tpl"}
