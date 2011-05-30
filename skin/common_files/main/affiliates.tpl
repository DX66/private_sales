{*
$Id: affiliates.tpl,v 1.1.2.1 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_affiliates_tree}
{$lng.txt_affiliates_tree_note}<br /><br />

<br />
 
{if $usertype ne 'B'}

  {capture name=dialog}

    <form action="affiliates.php" method="get">

      <table>

        <tr>
          <td valign="top">{$lng.lbl_partner_as_root}:</td>
          <td>
            <select name="affiliate" size="5">
              {foreach from=$partners item=v}
                <option value="{$v.id|escape}"{if $v.id eq $affiliate} selected="selected"{/if}>{$v.firstname} {$v.lastname}</option>
              {/foreach}
            </select>
          </td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td><input type="submit" value="{$lng.lbl_select|strip_tags:false|escape}" /></td>
        </tr>

      </table>
    </form>
  {/capture}
  {include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_select extra='width="100%"'}

  <br />

{/if}

{if $affiliate or $usertype eq 'B'}

  {capture name=dialog}
    <strong>{$lng.lbl_note}:</strong> 
    {if $usertype ne 'B'}
      {$lng.txt_affiliates_tree_comment_a}
    {else}
      {$lng.txt_affiliates_tree_comment_b}
    {/if}

    <br />
    <br />

    <table cellspacing="1" cellpadding="3" width="100%">

      <tr class="TableHead">
        <td width="100%">{$lng.lbl_partner}</td>
        <td align="center">{$lng.lbl_commission}</td>
        <td align="center" nowrap="nowrap">{$lng.lbl_affiliate_commission}</td>
      </tr>
      <tr>
        <td nowrap="nowrap">{$parent_affiliate.firstname} {$parent_affiliate.lastname}</td>
        <td align="right">{currency value=$parent_affiliate.sales|default:0}</td>
        <td align="right">{currency value=$parent_affiliate.childs_sales|default:0}</td>
      </tr>

      {include file="main/affiliate_list.tpl" level=0}

    </table>

  {/capture}
  {include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_affiliates extra='width="100%"'}

{/if}
