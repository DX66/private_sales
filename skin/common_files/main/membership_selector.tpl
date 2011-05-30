{*
$Id: membership_selector.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $field eq ''}
  {assign var="field" value="membershipids[]"}
{/if}
{assign var="size" value=1}

{if $memberships}
  {count assign="size" value=$memberships print=false}
  {inc assign="size" value=$size}

  {if $size gt 5}
    {assign var="size" value=5}
  {/if}

{/if}

<select name="{$field}" multiple="multiple" size="{$size}">
  <option value="-1"{if $data.membershipids eq ""} selected="selected"{/if}>{$lng.lbl_all}</option>
  {if $memberships}
    {foreach from=$memberships item=v}
      <option value="{$v.membershipid}"{if $data.membershipids ne "" and $data.membershipids[$v.membershipid] ne ''} selected="selected"{/if}>{$v.membership}</option>
    {/foreach}
  {/if}
</select>
{if $is_short ne 'Y'}
  <p>{$lng.lbl_hold_ctrl_key}</p>
{/if}
