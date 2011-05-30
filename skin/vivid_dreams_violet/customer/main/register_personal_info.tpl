{*
$Id: register_personal_info.tpl,v 1.1 2010/05/21 08:33:06 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $is_areas.P eq 'Y'}

{if $hide_header eq ""}
      <tr>
        <td colspan="3" class="register-section-title">
          <div>
            <label>{$lng.lbl_personal_information}</label>
          </div>
        </td>
      </tr>
{/if}

{if $default_fields.title.avail eq 'Y'}
      <tr>
        <td class="data-name"><label for="title">{$lng.lbl_title}</label></td>
        <td{if $default_fields.title.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
        <td>
          {include file="main/title_selector.tpl" val=$userinfo.titleid}
        </td>
      </tr>
{/if}

{if $default_fields.firstname.avail eq 'Y'}
      <tr>
        <td class="data-name"><label for="firstname">{$lng.lbl_first_name}</label></td>
        <td{if $default_fields.firstname.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
        <td>
          <input type="text" id="firstname" name="firstname" size="32" maxlength="32" value="{$userinfo.firstname|escape}" />
        </td>
      </tr>
{/if}
{if $default_fields.lastname.avail eq 'Y'}
      <tr>
        <td class="data-name"><label for="lastname">{$lng.lbl_last_name}</label></td>
        <td{if $default_fields.lastname.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
        <td>
          <input type="text" id="lastname" name="lastname" size="32" maxlength="32" value="{$userinfo.lastname|escape}" />
        </td>
      </tr>
{/if}
{if $default_fields.company.avail eq 'Y'}
      <tr>
        <td class="data-name"><label for="company">{$lng.lbl_company}</label></td>
        <td{if $default_fields.company.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
        <td>
          <input type="text" id="company" name="company" size="32" maxlength="255" value="{$userinfo.company|escape}" />
        </td>
      </tr>
{/if}
{if $default_fields.url.avail eq 'Y'}
      <tr>
        <td class="data-name"><label for="url">{$lng.lbl_web_site}</label></td>
        <td{if $default_fields.url.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
        <td>
          <input type="text" id="url" name="url" size="32" maxlength="128" value="{$userinfo.url|escape}" />
        </td>
      </tr>
{/if}
{if $default_fields.ssn.avail eq 'Y'}
      <tr>
        <td class="data-name"><label for="ssn">{$lng.lbl_ssn}</label></td>
        <td{if $default_fields.ssn.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
        <td>
          <input type="text" id="ssn" name="ssn" size="32" maxlength="32" value="{$userinfo.ssn|escape}" />
        </td>
      </tr>
{/if}
{if $default_fields.tax_number.avail eq 'Y'}
      <tr>
        <td class="data-name"><label for="tax_number">{$lng.lbl_tax_number}</label></td>
        <td{if $default_fields.tax_number.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
        <td>
{if $userinfo.tax_exempt ne "Y" or $config.Taxes.allow_user_modify_tax_number eq "Y" or $usertype eq "A" or $usertype eq "P"}
          <input type="text" id="tax_number" name="tax_number" size="32" maxlength="32" value="{$userinfo.tax_number|escape}" />
{else}
{$userinfo.tax_number}
{/if}
        </td>
      </tr>
{/if}
{if $config.Taxes.enable_user_tax_exemption eq 'Y'}
{if (($userinfo.usertype eq "C" or $smarty.get.usertype eq "C") and $userinfo.tax_exempt eq "Y") or ($usertype eq "A" or $usertype eq "P")}
      <tr>
        <td class="data-name"><label for="tax_exempt">{$lng.lbl_tax_exemption}</label></td>
        <td>&nbsp;</td>
        <td>
{if $usertype eq "A" or $usertype eq "P"} 
          <input type="checkbox" class="exclude-style" id="tax_exempt" name="tax_exempt" value="Y"{if $userinfo.tax_exempt eq "Y"} checked="checked"{/if} />
{elseif $userinfo.tax_exempt eq "Y"}
{$lng.txt_tax_exemption_assigned}
{/if}
        </td>
      </tr>
{/if}
{/if}
{if $usertype eq "A" or $usertype eq "P"}
      <tr>
        <td class="data-name"><label for="referer">{$lng.lbl_referred_by}</label></td>
        <td>&nbsp;</td>
        <td>
{if $userinfo.referer}
          <a href="{$userinfo.referer}">{$userinfo.referer}</a>
{else}
{$lng.lbl_unknown}
{/if}
        </td>
      </tr>
{/if}
{include file="customer/main/register_additional_info.tpl" section="P"}
{/if}
