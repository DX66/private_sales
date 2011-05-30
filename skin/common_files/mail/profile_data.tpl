{*
$Id: profile_data.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{$lng.lbl_account_information}:
---------------------
{if $config.email_as_login ne 'Y'}{$lng.lbl_username|mail_truncate}{$userinfo.login}
{/if}
{$lng.lbl_email|mail_truncate}{$userinfo.email}
{if $show_pwd}{$lng.lbl_password|mail_truncate}{$userinfo.password}
{/if}

{$lng.lbl_personal_information}:
---------------------
{if $userinfo.default_fields.title}{$lng.lbl_title|default:'-'|mail_truncate}{$userinfo.title}
{/if}
{if $userinfo.default_fields.firstname}{$lng.lbl_first_name|default:'-'|mail_truncate}{$userinfo.firstname}
{/if}
{if $userinfo.default_fields.lastname}{$lng.lbl_last_name|default:'-'|mail_truncate}{$userinfo.lastname}
{/if}
{if $userinfo.default_fields.company}{$lng.lbl_company|default:'-'|mail_truncate}{$userinfo.company}
{/if}
{if $userinfo.default_fields.url}{$lng.lbl_url|default:'-'|mail_truncate}{$userinfo.url}
{/if}
{if $userinfo.default_fields.ssn}{$lng.lbl_ssn|default:'-'|mail_truncate}{$userinfo.ssn}
{/if}
{if $userinfo.default_fields.tax_number}{$lng.lbl_tax_number|default:'-'|mail_truncate}{$userinfo.tax_number}
{/if}
{if $userinfo.tax_exempt eq 'Y'}{$lng.lbl_tax_exempt|default:'-'|mail_truncate}{$lng.txt_tax_exemption_assigned}
{/if}
{if $userinfo.membership}{$lng.lbl_membership|default:'-'|mail_truncate}{$userinfo.membership}
{/if}
{if $userinfo.pending_membershipid ne $userinfo.membershipid}{$lng.lbl_signup_for_membership|default:'-'|mail_truncate}{$userinfo.pending_membership}
{/if}
{foreach from=$userinfo.additional_fields item=v}{if $v.section eq 'P'}
{$v.title|default:'-'|mail_truncate}{$v.value}
{/if}{/foreach}

{foreach from=$userinfo.additional_fields item=v}{if $v.section eq 'C' or $v.section eq 'A'}
{$v.title|default:'-'|mail_truncate}{$v.value}
{/if}{/foreach}

