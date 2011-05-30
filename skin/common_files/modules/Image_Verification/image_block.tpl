{*
$Id: image_block.tpl,v 1.3.2.1 2010/11/15 11:46:25 ferz Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="iv-img">
	<img src="{$xcart_web_dir}/antibot_image.php?section={$id}&amp;rnd={"1"|mt_rand:10000}" id="{$id}" alt="" /><br />
{if $is_ajax_request eq "Y"}
<a href="javascript:void(0);" onclick="javascript: change_antibot_image('{$id}');">{$lng.lbl_get_a_different_code|wm_remove|escape:javascript}</a>
{else}
<script type="text/javascript">
//<![CDATA[
document.write('<'+'a href="javascript:void(0);" onclick="javascript: change_antibot_image(\'{$id}\');">{$lng.lbl_get_a_different_code|wm_remove|escape:javascript}<'+'/a>');
//]]>
</script>
{/if}
</div>
{if !$nobr}<br />{/if}
