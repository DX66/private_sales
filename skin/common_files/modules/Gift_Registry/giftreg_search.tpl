{*
$Id: giftreg_search.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_giftreg_search}</h1>

{capture name=dialog}

  <form name="searchgiftregform" action="giftregs.php" method="post">
    <table cellspacing="0" class="data-name" summary="{$lng.lbl_giftreg_search|escape}">

      <tr>
        <td class="data-name">{$lng.lbl_giftreg_creator_name}:</td>
        <td class="data-required">&nbsp;</td>
        <td><input type="text" name="post_data[name]" size="35" value="{$search_data.name|escape:"html"}" /></td>
      </tr>

      <tr> 
        <td class="data-name">{$lng.lbl_giftreg_creator_email}:</td>
        <td class="data-required">&nbsp;</td>
        <td><input type="text" name="post_data[email]" size="35" value="{$search_data.email|escape:"html"}" /></td>
      </tr>

      <tr>
        <td class="data-name">{$lng.lbl_keyword}:</td>
        <td class="data-required">&nbsp;</td>
        <td><input type="text" name="post_data[substring]" size="50" value="{$search_data.substring|escape:"html"}" /></td>
      </tr>

      <tr>
        <td colspan="2">&nbsp;</td>
        <td>
          <label>
            <input type="checkbox" name="post_data[inc_description]" value="Y"{if $search_data.inc_description eq "Y"} checked="checked"{/if} />
            {$lng.lbl_search_description}
          </label>
        </td>
      </tr>

      <tr> 
        <td class="data-name">{$lng.lbl_giftreg_event_status}:</td>
        <td class="data-required">&nbsp;</td>
        <td>
          <select name="post_data[status]">
            <option value="">{$lng.lbl_all}</option>
            <option value="P"{if $search_data.status eq "P"} selected="selected"{/if}>{$lng.lbl_private}</option>
            <option value="G"{if $search_data.status eq "G"} selected="selected"{/if}>{$lng.lbl_public}</option>
          </select>
        </td>
      </tr>

      {inc value=$config.Company.end_year assign="endyear" inc=3}
      <tr> 
        <td class="data-name">{$lng.lbl_giftreg_event_date_from}:</td>
        <td class="data-required">&nbsp;</td>
        <td>{include file="main/datepicker.tpl" name="start_date" date=$search_data.start_date end_year=$endyear}</td>
      </tr>

      <tr> 
        <td class="data-name">{$lng.lbl_giftreg_event_date_through}:</td>
        <td class="data-required">&nbsp;</td>
        <td>{include file="main/datepicker.tpl" name="end_date" date=$search_data.end_date end_year=$endyear}</td>
      </tr>

      <tr> 
        <td colspan="2">&nbsp;</td>
        <td class="button-row">{include file="customer/buttons/search.tpl" type="input"}</td>
      </tr>

    </table>

  </form>

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_giftreg_search content=$smarty.capture.dialog noborder=true}

{if $smarty.get.mode eq "search"}

  <p>{$items_count} {$lng.lbl_events_found}</p>

{/if}

{if $search_result ne ""}

  {capture name=dialog}

    {include file="customer/main/navigation.tpl"}

    <table cellspacing="1" cellpadding="3" summary="{$lng.lbl_search_results|escape}">
      {foreach from=$search_result item=e}

        <tr>
          {if $e.status eq "P"}
          <td class="giftreg-private-status"><img src="{$ImagesDir}/spacer.gif" alt="{$lng.lbl_private|escape}" /></td>
          {elseif $e.status eq "G"}
          <td class="giftreg-public-status"><img src="{$ImagesDir}/spacer.gif" alt="{$lng.lbl_public|escape}" /></td>
          {else}
          <td class="giftreg-access-denied-status"><img src="{$ImagesDir}/spacer.gif" alt="{$lng.lbl_disabled|escape}" /></td>
          {/if}
          <td class="giftreg-event-information"><a href="giftregs.php?eventid={$e.event_id}" title="{$lng.lbl_event_info|escape}">{$e.event_date|date_format:"%B %e, %Y"} - {$e.title}</a></td>
          <td class="giftreg-creator-name" title="{$lng.lbl_giftreg_creator_name|escape}">{$e.firstname} {$e.lastname}</td>
          <td class="giftreg-products-count"><a href="giftregs.php?eventid={$e.event_id}" title="{$lng.lbl_wish_list|escape}">{$e.products} {$lng.lbl_products}</a></td>
        </tr>
        {if $e.description ne ""}
          <tr>
            <td colspan="3"><em>{$e.description}</em></td>
          </tr>
        {/if}

        <tr>
          <td colspan="4"><hr /></td>
        </tr>

      {/foreach}

    </table>

    {include file="customer/main/navigation.tpl"}

  {/capture}
  {include file="customer/dialog.tpl" title=$lng.lbl_search_results content=$smarty.capture.dialog}

{/if}
