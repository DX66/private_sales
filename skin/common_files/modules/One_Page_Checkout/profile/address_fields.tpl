{*
$Id: address_fields.tpl,v 1.2.2.6 2011/04/18 07:42:06 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $type eq 'S'}
  <div id="ship2diff_box">
{/if}

{if $login ne ''}
  {include file="modules/One_Page_Checkout/profile/address_book_link.tpl" type=$type change_mode='Y' addressid=`$address.id`}
  <input type="hidden" id="{$id_prefix}id" name="{$name_prefix}[id]" size="32" maxlength="32" value="{$address.id|escape}" />
{/if}

<ul{if $first} class="first"{/if}>

{foreach from=$default_fields item=f key=fname}

  {if $f.avail eq 'Y'}
    {assign var=label_for value="`$id_prefix``$fname`"}
    {getvar var=liclass func=func_tpl_get_user_field_cssclass current_field=$fname default_fields=$default_fields}
    <li class="{$liclass}">

      {capture name=regfield}

        {if $fname eq 'title'}

          {include file="main/title_selector.tpl" val=$address.titleid name="`$name_prefix`[title]" id="`$id_prefix`title"}

        {elseif $fname eq 'zipcode'}

          {include file="main/zipcode.tpl" val=$address.zipcode|default:$config.General.default_zipcode zip4=$address.zip4 name="`$name_prefix`[zipcode]" id="`$id_prefix`zipcode"}

        {elseif $fname eq 'state'}

          {assign var=label_for value=$name_prefix|replace:"[":"_"|replace:"]":""|cat:"_state"}
          {include file="main/states.tpl" states=$states name="`$name_prefix`[state]" default=$address.state|default:$config.General.default_state default_country=$address.country|default:$config.General.default_country country_name="`$id_prefix`country" style='class="input-style" style="width: 250px;"'}

        {elseif $fname eq 'country'}

          <select name="{$name_prefix}[country]" id="{$id_prefix}country" onchange="check_zip_code_field(this, $('#{$id_prefix}zipcode'))" class="input-style" style="width: 250px;">
            {foreach from=$countries item=c}
              <option value="{$c.country_code}"{if $address.country eq $c.country_code or ($c.country_code eq $config.General.default_country and $address.country eq "")} selected="selected"{/if}>{$c.country|amp}</option>
            {/foreach}
          </select>

        {elseif $fname eq 'county' and $config.General.use_counties eq 'Y'}

          {include file="main/counties.tpl" counties=$counties name="`$name_prefix`[county]" default=$address.county country_name="country" id="`$id_prefix`county"}

        {elseif $fname eq 'city' and $address.$fname eq ''}
          
          <input type="text" id="{$id_prefix}{$fname}" name="{$name_prefix}[{$fname}]" size="32" maxlength="32" value="{$config.General.default_city|escape}" />

        {else}

          <input type="text" id="{$id_prefix}{$fname}" name="{$name_prefix}[{$fname}]" size="32" maxlength="32" value="{$address.$fname|escape}" />

        {/if}
      {/capture}

      {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required=$f.required name=$f.title field=$label_for}

    </li>

    {if $liclass eq 'fields-group last'}
      <li class="clearing"></li>
    {/if}

  {/if}
{/foreach}

{if $default_fields.country.avail and $default_fields.state.avail}
  <li style="display:none">
    {include file="main/register_states.tpl" state_name="`$name_prefix`[state]" country_name="`$id_prefix`country" county_name="`$name_prefix`[county]" state_value=$address.state|default:$config.General.default_state county_value=$address.county}
  </li>
{/if}

</ul>

{if $type eq 'S'}
  </div>
{/if}
