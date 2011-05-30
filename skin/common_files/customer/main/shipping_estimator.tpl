{*
$Id: shipping_estimator.tpl,v 1.3.2.2 2010/10/11 11:54:57 aim Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $login eq ''}

  <div class="estimator-container">

    {if $userinfo ne ''}

      <strong>{$lng.lbl_destination}:</strong>

      {foreach from=$shipping_estimate_fields item=f key=k name=estimate}
        {if $userinfo.address.S eq ''}
          {assign var=k value="s_"|cat:$k}
        {/if}  
        {assign var=_fieldname value=$k|cat:'name'}
        {assign var=_field value=$userinfo.address.S.$_fieldname|default:$userinfo.address.S.$k|default:$userinfo.$_fieldname|default:$userinfo.$k}
        {if $f.avail eq 'Y' and $_field ne ''}
          {$_field}
          {if not $smarty.foreach.estimate.last}, {/if}{/if}
      {/foreach}
    
      {assign var=btitle value=$lng.lbl_change}

    {/if}

    <div class="button-row">
      {include file="customer/buttons/button.tpl" button_title=$btitle|default:$lng.lbl_estimate_shipping_cost href="javascript:popupOpen('popup_estimate_shipping.php');" style="link"}
    </div>

    <div class="smethods">
      {include file="customer/main/checkout_shipping_methods.tpl" simple_list=true}
    </div>

  </div>

  <hr />
{/if}
