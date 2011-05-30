{*
$Id: bottom.tpl,v 1.1 2010/05/21 08:31:57 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table width="100%" cellpadding="0" cellspacing="0">

{if $active_modules.Users_online ne "" or $login and $all_languages_cnt gt 1}
<tr>
  <td>
  <table width="100%">
    <tr>
{if $active_modules.Users_online ne ""}
      <td class="users-online-box">
        {include file="modules/Users_online/menu_users_online.tpl"}
      </td>
{/if}

{if $login and $all_languages_cnt gt 1}
      <td class="admin-language">
        <form action="{$smarty.server.REQUEST_URI|amp}" method="post" name="asl_form">
          <input type="hidden" name="redirect" value="{$smarty.server.QUERY_STRING|amp}" />
          {$lng.lbl_language}:
          <select name="asl" onchange="javascript: document.asl_form.submit()">
          {foreach from=$all_languages item=l}
          <option value="{$l.code}"{if $current_language eq $l.code} selected="selected"{/if}>{$l.language}</option>
          {/foreach}
          </select>
        </form>
      </td>
{/if}
      </tr>
    </table>
  </td>
</tr>
{/if}

<tr>
  <td class="HeadThinLine">
    <img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" />
  </td>
</tr>

<tr>
  <td class="BottomBox">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td class="Bottom" align="left">
          {include file="main/prnotice.tpl"}
        </td>
        <td class="Bottom" align="right">
          {include file="copyright.tpl"}
        </td>
      </tr>
    </table>
  </td>
</tr>

</table>
