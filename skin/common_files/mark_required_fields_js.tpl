{*
$Id: mark_required_fields_js.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){ldelim}

  markEmptyFields($('form[name={$form}]'));
  
  {if $errfields ne ''}
    {foreach from=$errfields key=f item=v}
      $('#{$f}').addClass('err');
    {/foreach}
  {/if}
{rdelim});
//]]>
</script>
