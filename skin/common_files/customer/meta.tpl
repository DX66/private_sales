{*
$Id: meta.tpl,v 1.3 2010/07/02 11:52:49 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
  <meta http-equiv="Content-Type" content="text/html; charset={$default_charset|default:"iso-8859-1"}" />
  <meta http-equiv="X-UA-Compatible" content="IE=8" />
  <meta http-equiv="Content-Script-Type" content="text/javascript" />
  <meta http-equiv="Content-Style-Type" content="text/css" />
  <meta http-equiv="Content-Language" content="{$shop_language}" />
{if $printable}
  <meta name="ROBOTS" content="NOINDEX,NOFOLLOW" />
{else}
  {meta type='description' page_type=$meta_page_type page_id=$meta_page_id}
  {meta type='keywords' page_type=$meta_page_type page_id=$meta_page_id}
{/if}
