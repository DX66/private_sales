{*
$Id: payment_form.tpl,v 1.3 2010/06/08 06:17:42 igoryan Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<form method="{$method}" action="{$request_url|amp}" name="process">
{foreach from=$fields key=fn item=fv}
<input type="hidden" name="{$fn}" value="{$fv|escape:"html"}" />
{/foreach}

{if $autosubmit}
<div id="text_box">
  <noscript>
    {$lng.txt_noscript_payment_note}
    <br />
    <input type="submit" value="{$lng.lbl_submit}" />
  </noscript>
</div>

<script type="text/javascript">
//<![CDATA[
if (document.getElementById('text_box'))
    document.getElementById('text_box').innerHTML = '{$lng.txt_script_payment_note|substitute:"payment":$payment|escape:"javascript"}';

document.process.submit();
//]]>
</script>

{/if}

{if $display_cc_form}
{include file="payments/payment_cc_form.tpl"}
{/if}

</form>
