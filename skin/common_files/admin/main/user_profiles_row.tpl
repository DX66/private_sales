{*
$Id: user_profiles_row.tpl,v 1.3 2010/08/04 12:06:38 igoryan Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<tr{cycle values=", class='TableSubHead'"}>
  <td>
  {$title}
  <input type="hidden" name="{$name_prefix}_data[{$field}][flag]" value="Y" />
  </td>
{foreach from=$usertypes_array item=to_disable key=utype}
  <td align="center">
  <input{if $recommended eq 'Y' and ($utype eq 'C' || $utype eq 'H')} class="rec-{$name_prefix}-{$field}"{/if} type="checkbox"{if not $to_disable} onclick="javascript: {if $recommended eq 'Y' and ($utype eq 'C' || $utype eq 'H')} $('#box_{$field}').css('display', $('input.rec-{$name_prefix}-{$field}').length == $('input.rec-{$name_prefix}-{$field}:checked').length ? 'none' : 'block'); {/if}document.getElementById('{$idprefix}_{$field}_{$utype}').disabled = !this.checked;" name="{$name_prefix}_data[{$field}][avail][{$utype}]"{/if}{if $avail.$utype eq "Y"} checked="checked"{/if}{if $to_disable} disabled="disabled"{/if} />
  &nbsp;/&nbsp;
  <input type="checkbox"{if not $to_disable} id="{$idprefix}_{$field}_{$utype}" name="{$name_prefix}_data[{$field}][required][{$utype}]"{/if}{if $required.$utype eq "Y"} checked="checked"{/if}{if $to_disable or $avail.$utype ne "Y"} disabled="disabled"{/if} />
{if $to_disable}
{if $required.$utype eq "Y" or $force_required}<input type="hidden" name="{$name_prefix}_data[{$field}][required][{$utype}]" value='Y' />{/if}
{if $avail.$utype eq "Y"}<input type="hidden" name="{$name_prefix}_data[{$field}][avail][{$utype}]" value='Y' />{/if}
{/if}
  </td>
{/foreach}
</tr>
