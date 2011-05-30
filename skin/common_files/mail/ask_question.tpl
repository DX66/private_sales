{*
$Id: ask_question.tpl,v 1.1 2010/05/21 08:32:13 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/mail_header.tpl"}

{$lng.lbl_customer_info}:
---------------------

{$lng.lbl_username|mail_truncate}: {$uname|escape}
{$lng.lbl_email|mail_truncate}: {$email|escape}
{if $phone}{$lng.lbl_phone|mail_truncate}: {$phone|escape}{/if}

---------------------
{$lng.eml_someone_ask_question_at|substitute:"STOREFRONT":$current_location:"productid":$productid:"product_name":$product}:

{$question|escape}

{include file="mail/signature.tpl"}
