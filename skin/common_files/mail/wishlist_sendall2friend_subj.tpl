{*
$Id: wishlist_sendall2friend_subj.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}{$lng.eml_wishlist_sendall2friend_subj|substitute:"sender":"`$userinfo.firstname` `$userinfo.lastname`"}
