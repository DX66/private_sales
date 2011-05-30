{*
$Id: location.tpl,v 1.3.2.2 2010/10/27 13:58:17 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $location ne ""}

<div id="location">
{strip}
{section name=position loop=$location}
{if $location[position].1 ne ""}
  <a href="{$location[position].1|amp}">
{/if}
    {$location[position].0|amp}
{if $location[position].1 ne ""}
  </a>
{/if}
{if not %position.last%}
&nbsp;{$config.Appearance.breadcrumbs_separator|amp}&nbsp;
{/if}
{/section}
{/strip}
</div>

{/if}

<!-- check javascript availability -->
<noscript>
  <table width="500" cellpadding="2" cellspacing="0" align="center">
  <tr>
    <td align="center" class="ErrorMessage">{$lng.txt_noscript_warning}</td>
  </tr>
  </table>
</noscript>

<table id="{$newid|default:"dialog-message"}" width="100%" {if ($alt_content eq "" and $alt_type eq "") and ($top_message.content eq "" or ($top_message.type eq "I" or $top_message.type eq ""))} style="display: none;"{/if}>
<tr>
  <td>
    <div class="dialog-message">
      <div class="box message-{$top_message.type|default:$alt_type|default:"I"}"{if $top_message.title} title="{$top_message.title|escape}"{/if}>

        <table width="100%">
        <tr>
{if $image_none ne "Y"}
          <td width="50" valign="top">
            <img class="dialog-img" src="{$ImagesDir}/spacer.gif" alt="" />
          </td>
{/if}
          <td align="left" valign="top">
            {$top_message.content|default:$alt_content}
{if $top_message.anchor ne ""}
            <div class="anchor">
              <a href="#{$top_message.anchor}">
                {$lng.lbl_go_to_last_edit_section}
                <img src="{$ImagesDir}/spacer.gif" alt="" />
              </a>
            </div>
{/if}
          </td>
        </tr>
        </table>

{if $top_message.no_close eq ""}
        <a href="javascript:void(0);" class="close-link" onclick="javascript: $('#{$newid|default:"dialog-message"}').hide(); return false;">
          <img src="{$ImagesDir}/spacer.gif" alt="{$lng.lbl_close|escape}" class="close-img" />
        </a>
{/if}
      </div>
    </div>
  </td>
</tr>
</table>
