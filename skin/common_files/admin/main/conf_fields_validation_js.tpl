{*
$Id: conf_fields_validation_js.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
var email_validation_regexp = /{$email_validation_regexp}/gi

var validationFields = [
{if $configuration}
{foreach from=$configuration item=conf_var}
{if $conf_var.validation}
{assign var="opt_comment" value="opt_`$conf_var.name`"}
  {ldelim}name: '{$conf_var.name}', validation: "{$conf_var.validation}", comment: "{$lng.$opt_comment|default:$conf_var.comment|wm_remove|escape:javascript}"{rdelim},
{/if}
{/foreach}
{/if}
  {ldelim}{rdelim}
];

var invalid_parameter_text = '{$lng.err_invalid_field_data|wm_remove|escape:javascript}';
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/conf_fields_validation.js"></script>
