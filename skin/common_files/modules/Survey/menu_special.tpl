{*
$Id: menu_special.tpl,v 1.1 2010/05/21 08:32:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $surveys_is_avail}
  <li>
    <a href="{$catalogs.customer}/survey.php">{$lng.lbl_survey_surveys}</a>
  </li>
{/if}
