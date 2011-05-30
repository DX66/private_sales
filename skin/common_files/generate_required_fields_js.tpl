{*
$Id: generate_required_fields_js.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
var requiredFields = [
{foreach from=$default_fields item=v key=k}
{if $v.required eq 'Y' and $v.avail eq 'Y'}
  ["{$k}", "{$v.title|strip|replace:'"':'\"'}"],
{/if}
{/foreach}
{foreach from=$additional_fields item=v key=k}
{if $v.required eq 'Y' and $v.type eq 'T'  and $v.avail eq 'Y'} 
  ["additional_values_{$v.fieldid}", "{$v.title|strip|replace:'"':'\"'}"],
{/if} 
{/foreach}
{if $anonymous eq "" or $config.General.enable_anonymous_checkout neq "Y"}
{if $config.email_as_login ne 'Y'}
  ["uname", "{$lng.lbl_username|strip|replace:'"':'\"'}"],
{/if}
  ["passwd1", "{$lng.lbl_password|strip|replace:'"':'\"'}"],
  ["passwd2", "{$lng.lbl_confirm_password|strip|replace:'"':'\"'}"],
{/if}
  false
];
//]]>
</script>
