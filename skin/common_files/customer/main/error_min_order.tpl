{*
$Id: error_min_order.tpl,v 1.1.2.1 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_warning}</h1>

{currency assign="tmp_value" value=$config.General.minimal_order_amount}
<div class="error-message text-block">{$lng.err_checkout_not_allowed_msg|substitute:"value":$tmp_value}</div>
{include file="customer/buttons/go_back.tpl"}
