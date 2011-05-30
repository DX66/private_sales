{*
$Id: survey_stats_printable.tpl,v 1.1 2010/05/21 08:32:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$survey.survey}</title>
{include file="meta.tpl"}
<link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
</head>
<body{$reading_direction_tag}>

<table cellpadding="10" cellspacing="0">
<tr>
  <td>
{include file="modules/Survey/survey_stats.tpl"}
  </td>
</tr>
</table>

</body>
</html>
