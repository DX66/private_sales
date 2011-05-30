{*
$Id: personal_info.tpl,v 1.2 2010/07/21 08:04:01 aim Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $is_areas.P eq 'Y'}

  {if not $hide_header}
    <h3>{$lng.lbl_personal_information}</h3>
  {/if}

  <ul{if $first} class="first"{/if}>
  {foreach from=$default_fields item=f key=fname}

    {if $f.avail eq 'Y'}

      {if $fname eq 'title' or $fname eq 'firstname' or $fname eq 'lastname'}
        {assign var=liclass value="fields-group"}
        {if $fname eq 'lastname'}
          {assign var=liclass value="fields-group last"}
        {/if}
      {else}
        {assign var=liclass value="single-field"}
      {/if}

      <li class="{$liclass}">

        {capture name=regfield}
          {if $fname eq "title"}
            {include file="main/title_selector.tpl" val=$f.titleid}
          {else}
            <input type="text" name="{$fname}" id="{$fname}" value="{$userinfo[$fname]|escape}" />
          {/if}
        {/capture}
        {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required=$f.required name=$f.title field=$fname}

      </li>

    {/if}
  {/foreach}
  </ul>

  {include file="modules/One_Page_Checkout/profile/additional_info.tpl" section="P"}
{/if}
