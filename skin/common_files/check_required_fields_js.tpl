{*
$Id: check_required_fields_js.tpl,v 1.1 2010/05/21 08:31:57 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{*
Use service array:
  requiredFields
array structure:
  array(id, name, shadow_flag)
where:
  id       - tag id
  name     - element name
*}
<script type="text/javascript" src="{$SkinDir}/js/check_required_fields_js.js"></script>
{if $fillerror ne ''}
  {include file="mark_required_fields_js.tpl" form=$formname|default:"registerform" errfields=$fillerror.fields}
{/if}
