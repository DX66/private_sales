{*
$Id: affiliate_list.tpl,v 1.1.2.1 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $affiliates}

  {count assign="count" value=$affiliates print=false}
  {dec assign="count" value=$count|default:0}

  {foreach from=$affiliates item=v key=k}

    <tr>
       <td style="padding-left: {multi x=$level+1 y=20}px;" class="xaff-atree-item{if $v.level_delta lte $parent_affiliate.max_level} xaff-atree-internal{/if}">
        {if $usertype ne 'B'}
          <a href="user_modify.php?user={$v.id|escape:"url"}&amp;usertype=B">{$v.firstname} {$v.lastname}</a>
        {else}
          {if $config.XAffiliate.display_subaffiliate_name eq 'Y'}
            {$v.firstname} {$v.lastname}
          {else}
            affiliate
          {/if}
          (level: {$v.level_delta})
        {/if}
      </td>
      <td nowrap="nowrap" align="right">{currency value=$v.sales|default:0}</td>
      <td nowrap="nowrap" align="right">{currency value=$v.childs_sales|default:0}</td>
    </tr>

    {if $v.childs ne ''}
      {include file="main/affiliate_list.tpl" affiliates=$v.childs level=$level+1}
    {/if}

  {/foreach}

{/if}
