{*
$Id: edit_image.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $idtag eq ''}
  {assign var="idtag" value="edit_image"}
{/if}
<img id="{$idtag}" src="{$xcart_web_dir}/image.php?type={$type}&amp;id={$id}&amp;ts={$smarty.now}{if $already_loaded}&amp;tmp=Y{/if}"{if $image_x ne 0} width="{$image_x}"{/if}{if $image_y ne 0} height="{$image_y}"{/if} alt="{include file="main/image_property.tpl"}" style="margin-bottom: 10px;" />

<table  cellpadding="0" cellspacing="0">
  <tr>
    <td>
      <input type="button" value="{$lng.lbl_change_image|strip_tags:false|escape}" onclick='javascript: popup_image_selection("{$type}", "{$id}", "{$idtag}");' />
      {if $id ne '' and not $no_delete and ($delete_url or $delete_js)}
        &nbsp;&nbsp;
        <input id="{$idtag}_delete" type="button" value="{$lng.lbl_delete_image|strip_tags:false|escape}" onclick="javascript: {if $delete_js ne ''}{$delete_js|replace:'"':'\"'}{else}self.location='{$delete_url}';{/if}" />
      {/if}
      <span style="{if not $already_loaded}display: none; {/if}padding-left: 10px;" id="{$idtag}_reset">
        <input type="button" value="{$lng.lbl_reset|strip_tags:false|escape}" onclick="javascript: popup_image_selection_reset('{$type}', '{$id}', '{$idtag}');" />
        <input id="skip_image_{$type}" type="hidden" name="skip_image[{$type}]" value="" />
      </span>
    </td>
  </tr>
  <tr style="display: none;" id="{$idtag}_text">
    {if $button_name eq ''}
      {assign var="button_name" value=$lng.lbl_submit}
    {/if}
    <td style="padding-top: 10px;">{$lng.txt_image_note|substitute:"button_name":$button_name}</td>
  </tr> 
</table>
