{*
$Id: language_selector.tpl,v 1.1.2.1 2010/11/15 08:54:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $all_languages_cnt gt 1}
  <div class="languages {if $config.Appearance.line_language_selector eq 'Y'}languages-row{elseif $config.Appearance.line_language_selector eq 'F'}languages-flags{else}languages-select{/if}">

    {if $config.Appearance.line_language_selector eq 'Y' or $config.Appearance.line_language_selector eq 'A' or $config.Appearance.line_language_selector eq 'L'}

      {foreach from=$all_languages item=l name=languages}
        {if $config.Appearance.line_language_selector eq 'Y'}
          {assign var="lng_dspl" value=$l.code3}
        {elseif $config.Appearance.line_language_selector eq 'A'}
          {assign var="lng_dspl" value=$l.code}
        {elseif $config.Appearance.line_language_selector eq 'L'}
          {assign var="lng_dspl" value=$l.language}
        {/if} 
        {if $store_language eq $l.code}
          <strong class="language-code lng-{$l.code}">{$lng_dspl|default:$l.language}</strong>
        {else}
          <a href="home.php?sl={$l.code}" class="language-code lng-{$l.code}">{$lng_dspl|default:$l.language}</a>
        {/if}
        {if not $smarty.foreach.languages.last}|{/if}
      {/foreach}

    {elseif $config.Appearance.line_language_selector eq 'F'}

      {foreach from=$all_languages item=l name=languages}
        {if $store_language eq $l.code}
          <strong class="language-code lng-{$l.code}{if $smarty.foreach.languages.last} language-last{/if}"><img src="{if not $l.is_url}{$current_location}{/if}{$l.tmbn_url|amp}" alt="{$l.language|escape}" width="{$l.image_x}" height="{$l.image_y}" title="{$l.language|escape}" /></strong>
        {else}
          <a href="home.php?sl={$l.code}" class="language-code lng-{$l.code}{if $smarty.foreach.languages.last} language-last{/if}"><img class="language-code-out" src="{if not $l.is_url}{$current_location}{/if}{$l.tmbn_url|amp}" alt="{$l.language|escape}" title="{$l.language|escape}" width="{$l.image_x}" height="{$l.image_y}" onmouseover="javascript:$(this).removeClass('language-code-out').addClass('language-code-over');" onmouseout="javascript:$(this).removeClass('language-code-over').addClass('language-code-out');" /></a>
        {/if}
      {/foreach}

    {else}

      <form action="home.php" method="get" name="sl_form">
        <input type="hidden" name="redirect" value="{$smarty.server.PHP_SELF}{if $smarty.server.QUERY_STRING}?{$smarty.server.QUERY_STRING|amp}{/if}" />

        {strip}
          <label>{$lng.lbl_select_language}:
          <select name="sl" onchange="javascript: this.form.submit();">
            {foreach from=$all_languages item=l}
              <option value="{$l.code}"{if $store_language eq $l.code} selected="selected"{/if}>{$l.language}</option>
            {/foreach}
          </select>
          </label>
        {/strip}

      </form>

    {/if}

  </div>
{/if}
