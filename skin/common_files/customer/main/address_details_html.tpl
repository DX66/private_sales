{*
$Id: address_details_html.tpl,v 1.1.2.1 2010/09/01 07:26:18 aim Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="address-line">
  {if $default_fields.title and $address.title ne ''}{$address.title|escape} {/if}
  {if $default_fields.firstname and $address.firstname ne ''}{$address.firstname|escape} {/if}
  {if $default_fields.lastname and $address.lastname ne ''}{$address.lastname|escape}{/if}
</div>

<div class="address-line">
  {if $default_fields.address and $address.address ne ''}{$address.address|escape},<br />{/if}
  {if $default_fields.address_2 and $address.address_2 ne ''}{$address.address_2|escape},<br />{/if}
  {if $default_fields.city and $address.city ne ''}{$address.city|escape}, {/if}
  {if $default_fields.state and $address.state ne ''}{$address.statename|default:$address.state|escape}, {/if}
  {if $default_fields.county and  $address.county ne ''}{$address.countyname|default:$address.county|escape}, <br />{/if}
  {if $default_fields.zipcode and $address.zipcode ne ''}{include file="main/zipcode.tpl" val=$address.zipcode zip4=$address.zip4 static=true}<br />{/if}
  {if $default_fields.country and $address.country ne ''}{$address.countryname|default:$address.country|escape}{/if}
</div>

<div class="address-line">
  {if $default_fields.phone and $address.phone ne ''}{$lng.lbl_phone}: {$address.phone|escape}{/if}<br />
  {if $default_fields.fax and $address.fax ne ''}{$lng.lbl_fax}: {$address.fax|escape}{/if}
</div>
