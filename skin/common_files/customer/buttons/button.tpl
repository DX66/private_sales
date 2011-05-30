{*
$Id: button.tpl,v 1.2 2010/06/18 11:04:33 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{assign var="js_link" value=$href|regex_replace:"/^\s*javascript\s*:/Si":""}
{if $js_link eq $href}

  {if $href}
    {assign var="is_link" value=true}
  {/if}

  {assign var="js_link" value=false}
  {assign var="href" value=$href|amp}

{else}

  {assign var="js_link" value=$href}
  {if $js_to_href ne 'Y'}

    {assign var="onclick" value=$href}
    {if $link_href}
      {assign var="href" value=$link_href}
    {else}
      {assign var="href" value="javascript:void(0);"}
    {/if}

  {/if}
{/if}

{if $style eq 'link'}

  {if $type eq 'input'}

    <button class="simple-button{if $additional_button_class} {$additional_button_class}{/if}" type="submit" title="{$title|default:$button_title|escape}"{if $js_link} onclick="{$js_link}"{/if}>
      {strip}
        <img class="left-simple-button" src="{$ImagesDir}/spacer.gif" alt="" />
        <span>{$button_title|amp}</span>
        <img class="right-simple-button" src="{$ImagesDir}/spacer.gif" alt="" />
      {/strip}
    </button>

  {else}

    {strip}
      <a class="simple-button{if $additional_button_class} {$additional_button_class}{/if}" href="{$href|amp}"
        {if $onclick ne ''} onclick="{$onclick}; return false;"{/if} title="{$title|default:$button_title|escape}"
        {if $target ne ''} target="{$target}"{/if}
      >
        <span>{$button_title|amp}</span>
      </a>
    {/strip}

  {/if}

{elseif $style eq 'image'}

  {if $type eq 'input'}

    <input class="image-button{if $additional_button_class} {$additional_button_class}{/if}" type="image" src="{$ImagesDir}/spacer.gif" alt="{$title|default:$button_title|escape}"{if $js_link} onclick="{$js_link}"{/if}{if $button_id} id="{$button_id|escape}"{/if} />

  {else}

    {strip}
      <a class="image-button{if $additional_button_class} {$additional_button_class}{/if}" href="{$href|amp}"
        {if $onclick ne ''} onclick="{$onclick}"{/if} title="{$title|default:$button_title|escape}"
        {if $target ne ''} target="{$target}"{/if}
      >
        <img src="{$ImagesDir}/spacer.gif" alt="" />
      </a>
    {/strip}

  {/if}

{elseif $is_link}

  {if $js_link}
    {assign var="div_link" value=$js_link}
  {else}
    {assign var="div_link" value=$href|amp}
    {assign var="div_link" value="javascript: self.location = '`$div_link`'; if (event) event.cancelBubble = true;"}
  {/if}
  <div class="button{if $additional_button_class} {$additional_button_class}{/if}" title="{$title|default:$button_title|escape}" onclick="{$div_link}"{if $button_id} id="{$button_id|escape}"{/if}>
    <a href="{$href}" onclick="{if $js_link}{$js_link};{else}javascript:{/if} if (event) event.cancelBubble = true;"{$reading_direction_tag}>{$button_title|amp}</a>

  </div>

{elseif $style eq 'dropout'}

  <div class="dropout-wrapper">
    <div class="dropout-container">
      <div class="button{if $additional_button_class} {$additional_button_class}{/if}" title="{$title|default:$button_title|escape}" id="dropout_btn_{$prefix|default:'dropout'}_{$dropout_id}">
        <div{$reading_direction_tag}>{$button_title|amp}</div>
      </div>
      <div class="dropout-box">
      {if $dropout_tpl ne ""}
        <ul>
          {assign var=style value=false}
          {include file=$dropout_tpl}
        </ul>
      {/if}
      </div>
    </div>
  </div>

{elseif $style eq 'div_button'}

  <div class="button{if $additional_button_class} {$additional_button_class}{/if}" title="{$title|default:$button_title|escape}"{if $js_link} onclick="{$js_link}"{/if}{if $button_id} id="{$button_id|escape}"{/if}>
    <div{$reading_direction_tag}>{$button_title|amp}</div>
  </div>

{else}

  <button class="button{if $additional_button_class} {$additional_button_class}{/if}" type="{if $type eq 'input'}submit{else}button{/if}" title="{$title|default:$button_title|escape}"{if $js_link} onclick="{$js_link}"{/if}{if $button_id} id="{$button_id|escape}"{/if}>
  {strip}
    <span class="button-right">
      <span class="button-left"{$reading_direction_tag}>{$button_title|amp}</span>
    </span>
  {/strip}
  </button>

{/if}
