{*
$Id: event_details_customer.tpl,v 1.3 2010/06/08 06:17:40 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$event_data.title}</h1>

{capture name=dialog}

  {$lng.lbl_giftreg_event_created_by} {$event_data.creator_title} {$event_data.firstname} {$event_data.lastname}
  <hr />

  {if $event_data.description}
    {$event_data.description|nl2br}<br />
  {/if}

  <br />

  {include file="customer/subheader.tpl" title=$lng.lbl_wish_list}

  {include file="modules/Wishlist/wl_products.tpl" wl_products=$wl_products script_name="giftreg" giftregistry="Y"}

{/capture}
{include file="customer/dialog.tpl" title=$event_data.title content=$smarty.capture.dialog noborder=true}

{if $event_data.guestbook eq "Y"}
  {include file="modules/Gift_Registry/event_guestbook.tpl"}
{/if}

{if $config.Gift_Registry.enable_html_cards eq "Y" and $event_data.html_content ne ""}
<script type="text/javascript">
//<![CDATA[
  window.open(
    "giftregs.php?eventid={$event_data.event_id}&mode=preview",
    "eventcard",
    "width=600,height=450,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no"
  );
//]]>
</script>
{/if}
