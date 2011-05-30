{*
$Id: popup_help_link.tpl,v 1.1 2010/05/21 08:32:04 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $section}
  <a href="popup_info.php?action={$section}" onclick="javascript: return typeof(window.popupOpen) == 'undefined' || !popupOpen('popup_info.php?action={$section}', '{$title|wm_remove|escape:javascript}');" class="popup-link" target="_blank"><img src="{$ImagesDir}/spacer.gif" alt="{$lng.lbl_popup_help|escape}" /></a>
{/if}
