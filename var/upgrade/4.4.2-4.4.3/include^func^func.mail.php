<?php
/* vim: set ts=4 sw=4 sts=4 et: */
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| X-Cart                                                                      |
| Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>                  |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE  |
| AT THE FOLLOWING URL: http://www.x-cart.com/license.php                     |
|                                                                             |
| THIS  AGREEMENT  EXPRESSES  THE  TERMS  AND CONDITIONS ON WHICH YOU MAY USE |
| THIS SOFTWARE   PROGRAM   AND  ASSOCIATED  DOCUMENTATION   THAT  RUSLAN  R. |
| FAZLYEV (hereinafter  referred to as "THE AUTHOR") IS FURNISHING  OR MAKING |
| AVAILABLE TO YOU WITH  THIS  AGREEMENT  (COLLECTIVELY,  THE  "SOFTWARE").   |
| PLEASE   REVIEW   THE  TERMS  AND   CONDITIONS  OF  THIS  LICENSE AGREEMENT |
| CAREFULLY   BEFORE   INSTALLING   OR  USING  THE  SOFTWARE.  BY INSTALLING, |
| COPYING   OR   OTHERWISE   USING   THE   SOFTWARE,  YOU  AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE  ACCEPTING  AND AGREEING  TO  THE TERMS OF THIS |
| LICENSE   AGREEMENT.   IF  YOU    ARE  NOT  WILLING   TO  BE  BOUND BY THIS |
| AGREEMENT, DO  NOT INSTALL OR USE THE SOFTWARE.  VARIOUS   COPYRIGHTS   AND |
| OTHER   INTELLECTUAL   PROPERTY   RIGHTS    PROTECT   THE   SOFTWARE.  THIS |
| AGREEMENT IS A LICENSE AGREEMENT THAT GIVES  YOU  LIMITED  RIGHTS   TO  USE |
| THE  SOFTWARE   AND  NOT  AN  AGREEMENT  FOR SALE OR FOR  TRANSFER OF TITLE.|
| THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY GRANTED BY THIS AGREEMENT.      |
|                                                                             |
| The Initial Developer of the Original Code is Ruslan R. Fazlyev             |
| Portions created by Ruslan R. Fazlyev are Copyright (C) 2001-2011           |
| Ruslan R. Fazlyev. All Rights Reserved.                                     |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

/**
 * Mail functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.mail.php,v 1.40.2.1 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('files');

function func_mail_quote($string, $charset)
{
    return "=?".$charset."?B?".base64_encode($string)."?=";
}

/**
 * Check that length of the longest email body line does not exceed SMTP 998 characters limit.
 */
function func_mail_length_check(&$content)
{
    return !preg_match("/^.{999,}$/m", $content);
}

/**
 * Encode mail content using base64 algorithm.
 */
function func_mail_enc_base64(&$content)
{
    return rtrim(chunk_split(base64_encode($content)));
}

/**
 * Check if email address corresponds to current domain
 */
function func_mail_domain_check($mail_addr)
{
    global $HTTP_HOST;

    $matches = array();
    $result = false;

    if (preg_match("/[^ @,;<>]+@([^ @,;<>]+)/S", $mail_addr, $mathches) && !empty($mathches[1])) {
        $result = preg_match("/[\w\d\.]*\.".$mathches[1].'/S', $HTTP_HOST);
    }

    return $result;
}

/**
 * Returns SMTP connection handling error message.
 */
function func_get_smtp_server_error(&$smtp, $message = '', $errno_field = "smtp_code", $err_msg_field = "smtp_msg")
{
    $result_message = '';
    if (!empty($message)) {
        $result_message .= $message;
    }
    if (!empty($smtp->error)) {
        $result_message .= ' (';
        if (!empty($smtp->error['error'])) {
            $result_message .= 'Error: ' . trim($smtp->error['error']);
        }
        if (!empty($smtp->error[$errno_field])) {
            $result_message .= '; ' . $errno_field . ': ' . trim($smtp->error[$errno_field]);
        }
        if (!empty($smtp->error[$err_msg_field])) {
            $result_message .= '; ' . $err_msg_field . ': ' . trim($smtp->error[$err_msg_field]);
        }
        $result_message .= ')';
    }

    return $result_message;
}

/**
 * Send mail using SMTP auth.
 */
function func_smtp_send_mail($mail_data)
{
    global $xcart_dir;
    global $config;

    $result = array('status' => false);
    if (empty($config['Email']['smtp_server'])) {
        $result['message'] = "Please specify SMTP server address at General settings > Email options page.";

        return $result;
    }

    require_once $xcart_dir.'/include/xcartmailer.php';
    $smtp = new XcartMailer();

    $port = intval($config['Email']['smtp_port']);

    // Initiate connection.
    if (!$smtp->Connect($config['Email']['smtp_server'], $port)) {
        $result['message'] = func_get_smtp_server_error($smtp, 'Failed to connect to SMTP server', 'errno', 'errstr');
        $smtp->Close();

        return $result;
    }

    // Send greeting.
    if (!$smtp->Hello()) {
        $result['message'] = func_get_smtp_server_error($smtp, 'Failed to complete SMTP handshake');
        $smtp->Close();

        return $result;
    }

    // Perform SMTP authentication.
    if (!empty($config['Email']['smtp_user'])) {
        $smtp->setAuthMethod($config['Email']['smtp_auth_method']);
        if (!$smtp->Authenticate($config['Email']['smtp_user'], $config['Email']['smtp_password'])) {
            $result['message'] = func_get_smtp_server_error($smtp, 'SMTP authentication failed (SMTP AUTH method: '.(empty($config['Email']['smtp_auth_method']) ? 'AUTO' : $config['Email']['smtp_auth_method']).')');
            $smtp->Close();

            return $result;
        }
    }

    // Determine SMTP MAIL FROM email address.
    $smtp_from = $config['Email']['smtp_mail_from'];
    if (empty($smtp_from)) {
        // If SMTP MAIL FROM address is empty, let's try to use default email addresses
        $smtp_from = empty($config['Company']['company_mail_from']) ? $config['Company']['site_administrator'] : $config['Company']['company_mail_from'];
    }
    // Reset connection and return error if we failed to determine correct SMTP MAIL FROM email address.
    if (empty($smtp_from)) {
        $smtp->Reset();
        $smtp->Quit();
        $result['message'] = 'Failed to send email, because MAIL FROM address is empty. Please specify correct address at General settings > Email options page';

        return $result;
    }

    // Send SMTP MAIL FROM email address.
    if (!$smtp->Mail($smtp_from)) {
        $result['message'] = func_get_smtp_server_error($smtp, 'Email server declined MAIL FROM address');
        $smtp->Close();

        return $result;
    }

    // Send recipients email addresses.
    $recipients = preg_split("/[,\s]+/", $mail_data['to']);
    $recipients = func_array_map('trim', $recipients);
    foreach ($recipients as $r) {
        if (empty($r)) {
            continue;
        }
        if (!$smtp->Recipient($r)) {
            $result['message'] = func_get_smtp_server_error($smtp, 'Email server declined recipient email address');
            $smtp->Close();

            return $result;
        }
    }

    // Send email data.
    if (!$smtp->Data("To:" . $mail_data['to'] . $mail_data['lend'] . "Subject: " . $mail_data['mail_subject'] . $mail_data['lend'] . $mail_data['headers'] . $mail_data['lend'] . $mail_data['mail_message'])) {
        $result['message'] = func_get_smtp_server_error($smtp, 'Email server failed to send email data');
        $smtp->Close();

        return $result;
    }

    // Quit and close connection.
    $smtp->Quit();
    $result['status'] = true;

    return $result;
}

/**
 * Send mail abstract function
 * $from - from/reply-to address
 */
function func_send_mail($to, $subject_template, $body_template, $from, $to_admin, $crypted = false) 
{
    global $mail_smarty, $sql_tbl;
    global $config;
    global $current_language, $store_language, $shop_language, $all_languages;
    global $to_customer;
    global $override_lng_code;

    if (empty($to)) return;

    $from = preg_replace('![\x00-\x1f].*$!sm', '', $from);

    $encrypt_mail = $crypted && $config['Security']['crypt_method'];

    $lng_code = '';

    if ($to_admin) {

        $lng_code = ($current_language ? $current_language : $config['default_admin_language']);

    } elseif ($to_customer) {

        $lng_code = $to_customer;

    } else {

        $lng_code = $shop_language;

    }

    $charset = $all_languages[$lng_code]['charset'];

    $override_lng_code = $lng_code;

    $mail_smarty->assign_by_ref ('config', $config);

    $lend = (X_DEF_OS_WINDOWS?"\r\n":"\n");

    // Get mail subject
    $mail_subject = chop(func_display($subject_template, $mail_smarty, false));

    // Get messages array
    $msgs = array(
        'header' => array (
            "Content-Type" => "multipart/related;$lend\ttype=\"multipart/alternative\""
        ),
        'content' => array()
    );

    if ($config['Email']['html_mail'] != 'Y')
        $mail_smarty->assign('plain_text_message', 1);

    $mail_message = func_display($body_template,$mail_smarty,false);

    if (X_DEF_OS_WINDOWS) {
        $mail_message = preg_replace("/(?<!\r)\n/S", "\r\n", $mail_message);
    }

    if ($encrypt_mail)
        $mail_message = func_pgp_encrypt ($mail_message);

    $plaintext_encoding = '8bit';

    $plaintext_message = strip_tags($mail_message);

    if (!func_mail_length_check($plaintext_message)) {

        $plaintext_encoding = 'base64';

        $plaintext_message = func_mail_enc_base64($plaintext_message);

    }

    $msgs['content'][] = array (
        'header' => array (
            "Content-Type" => 'multipart/alternative'
        ),
        'content' => array (
            array (
                'header' => array (
                    "Content-Type" => "text/plain;$lend\tcharset=\"$charset\"",
                    "Content-Transfer-Encoding" => $plaintext_encoding
                ),
                'content' => html_entity_decode($plaintext_message, ENT_QUOTES),
            )
        )
    );

    if (
        $config['Email']['html_mail'] == 'Y'
        && !$encrypt_mail
        && func_template_exists('/mail/html/' . basename($body_template), $mail_smarty)
    ) {
            $mail_smarty->assign('mail_body_template', 'mail/html/' . basename($body_template));

            $mail_message = func_display('mail/html/html_message_template.tpl', $mail_smarty, false);

            list($mail_message, $files) = func_attach_images($mail_message);

            $htmlmail_encoding = '8bit';

            if (!func_mail_length_check($mail_message)) {
                $htmlmail_encoding = 'base64';
                $mail_message = func_mail_enc_base64($mail_message);
            }

            $msgs['content'][0]['content'][] = array (
                'header' => array (
                    "Content-Type" => "text/html;$lend\tcharset=\"$charset\"",
                    "Content-Transfer-Encoding" => $htmlmail_encoding
                ),
                'content' => $mail_message
            );

            if (!empty($files)) {

                foreach ($files as $v) {

                    $msgs['content'][] = array (
                        'header' => array (
                            "Content-Type"              => "$v[type];$lend\tname=\"$v[name]\"",
                            "Content-Transfer-Encoding" => 'base64',
                            "Content-ID"                => "<$v[name]>",
                        ),
                        'content' => chunk_split(base64_encode($v['data'])),
                    );

                }

            }

    }

    list($message_header, $mail_message) = func_parse_mail($msgs);

    $company_mail_from = $mail_from = $from;

    if ($config['Email']['use_base64_headers'] == 'Y')
        $mail_subject = func_mail_quote($mail_subject,$charset);

    if (
        !empty($config['Company']['company_mail_from']) 
        && !func_mail_domain_check($company_mail_from)
    ) {

        $company_mail_from = $config['Company']['company_mail_from'];

    }

    $headers = "From: " . $company_mail_from . $lend . "X-Mailer: X-Cart" . $lend . "MIME-Version: 1.0" . $lend . $message_header;

    if (trim($mail_from) != '')
        $headers .= "Reply-to: " . $mail_from.$lend;

    if ($config['Email']['use_smtp'] == 'Y') {

        $mail_data = array (
            'to'            => $to,
            'lend'          => $lend,
            'mail_subject'  => $mail_subject,
            'headers'       => $headers,
            'mail_message'  => $mail_message,
        );

        $smtp_result = func_smtp_send_mail($mail_data);

        if (!$smtp_result['status']) {

            x_log_add('smtp_mail', $smtp_result['message']);

        }

        return $smtp_result['status'];

    } else {

        return @mail($to, $mail_subject, $mail_message, $headers);

    }

}

/**
 * Parse tree of messages to message header and body
 */
function func_parse_mail($msgs, $level = 0)
{

    if (empty($msgs))
        return false;

    $lend = (X_DEF_OS_WINDOWS?"\r\n":"\n");
    $head = '';
    $msg = '';

    // Subarray
    if (is_array($msgs['content'])) {
        // Subarray is full
        if(count($msgs['content']) > 1) {
            $boundary = substr(uniqid(XC_TIME+rand().'_'), 0, 16);
            $msgs['header']['Content-Type'] .= ";$lend\t boundary=\"$boundary\"";
            foreach($msgs['header'] as $k => $v)
                $head .= $k.": ".$v.$lend;

            if($level > 0)
                $msg = $head.$lend;

            for($x = 0; $x < count($msgs['content']); $x++) {
                $res = func_parse_mail($msgs['content'][$x], $level+1);
                $msg .= "--".$boundary.$lend.$res[1].$lend;
            }

            $msg .= "--".$boundary."--".$lend;
        } else {
            // Subarray have only one element
            list($msgs['header'], $msgs['content']) = func_parse_mail($msgs['content'][0], $level);
        }
    }

    // Current array - atom
    if (!is_array($msgs['content'])) {
        if (is_array($msgs['header']))
            foreach ($msgs['header'] as $k => $v)
                $head .= $k.": ".$v.$lend;

        if ($level > 0)
            $msg = $head.$lend;

        $msg .= $msgs['content'].$lend;
    }

    // Header substitute
    if (empty($head)) {
        if (is_array($msgs['header'])) {
            foreach ($msgs['header'] as $k => $v)
                $head .= $k.": ".$v.$lend;
        } else {
            $head = $msgs['header'];
        }
    }

    return array($head, $msg);
}

/**
 * Send mail using prepared $body as source (non-templates based)
 */
function func_send_simple_mail($to, $subject, $body, $from, $extra_headers=array())
{
    global $config;
    global $current_language, $all_languages;
    global $sql_tbl;

    if (empty($to)) return;

    $from = preg_replace('![\x00-\x1f].*$!sm', '', $from);

    if (X_DEF_OS_WINDOWS) {
        $body = preg_replace("/(?<!\r)\n/S", "\r\n", $body);
        $lend = "\r\n";
    }
    else {
        $lend = "\n";
    }

    // Skip charset selection using a SQL query if we send notification about SQL errors.
    // In other case we might get into endless recursive call (db_error_generic function).
    if (defined('SKIP_CHARSET_SELECTION')) {
        $charset = 'iso-8859-1';
    } else {
        if (!empty($current_language))
            $charset = $all_languages[$current_language]['charset'];

        if (empty($charset))
            $charset = $all_languages[$config['default_admin_language']]['charset'];
    }

    $m_from = $from;
    $m_subject = $subject;

    if ($config['Email']['use_base64_headers'] == 'Y') {
        $m_subject = func_mail_quote($m_subject,$charset);
    }

    $headers = array (
        "X-Mailer" => "X-Cart",
        "MIME-Version" => '1.0',
        "Content-Type" => 'text/plain'
    );

    if (trim($m_from) != '') {
        $headers['From'] = $m_from;
        $headers["Reply-to"] = $m_from;
    }

    $headers = func_array_merge($headers, $extra_headers);

    if (strpos($headers["Content-Type"], "charset=") === FALSE)
        $headers["Content-Type"] .= "; charset=".$charset;

    if (!func_mail_length_check($body)) {
        $headers['Content-Transfer-Encoding'] = 'base64';
        $body = func_mail_enc_base64($body);
    }

    $headers_str = '';
    foreach ($headers as $hfield=>$hval)
        $headers_str .= $hfield.": ".$hval.$lend;
    if ($config['Email']['use_smtp'] == 'Y') {
        $mail_data = array (
            'to' => $to,
            'lend' => $lend,
            'mail_subject' => $m_subject,
            'headers' => $headers_str,
            'mail_message' => $body
        );
        $smtp_result = func_smtp_send_mail($mail_data);
        if (!$smtp_result['status']) {
            x_log_add('smtp_mail', $smtp_result['message']);
        }

        return $smtp_result['status'];
    } else {
        if (preg_match('/([^ @,;<>]+@[^ @,;<>]+)/S', $from, $m))
            return @mail($to,$m_subject,$body,$headers_str, "-f".$m[1]);
        else
            return @mail($to,$m_subject,$body,$headers_str);
    }
}

function func_pgp_encrypt($message)
{
    global $config;

    if (!$config['Security']['crypt_method']) {
        return $message;
    }

    if (($config['Security']['crypt_method'] == 'G' && empty($config["Security"]["gpg_key"])) ||
        $config['Security']['crypt_method'] != 'G' && empty($config["Security"]["pgp_key"]))
        return $message;

    $fn = func_temp_store($message);
    $gfile = func_temp_store('');

    if ($config['Security']['crypt_method'] == 'G') {

        putenv("GNUPGHOME=".$config['Security']['gpg_home_dir']);

        $gpg_prog = func_shellquote($config['Security']['gpg_prog']);
        $gpg_key = $config['Security']['gpg_key'];

        @exec($gpg_prog.' --always-trust -a --batch --yes --recipient "'.$gpg_key.'" --encrypt '.func_shellquote($fn)." 2>".func_shellquote($gfile));
    }
    else {

        putenv("PGPPATH=".$config['Security']['pgp_home_dir']);
        putenv("PGPHOME=".$config['Security']['pgp_home_dir']);

        $pgp_prog = func_shellquote($config['Security']['pgp_prog']);
        $pgp_key = $config['Security']['pgp_key'];

        if ($config['Security']['use_pgp6'] == 'Y') {
            @exec($pgp_prog." +batchmode +force -ea ".func_shellquote($fn)." \"$pgp_key\" 2>".func_shellquote($gfile));
        }
        else {
            @exec($pgp_prog.' +batchmode +force -fea "'.$pgp_key.'" < '.func_shellquote($fn).' > '.func_shellquote($fn).".asc 2>".func_shellquote($gfile));
        }
    }

    $af = dirname($fn).XC_DS.basename($fn).'.asc';
    $message = func_temp_read($af, true);
    $config['PGP_output'] = func_temp_read($gfile, true);
    @unlink($fn);

    return $message;
}

function func_pgp_remove_key()
{
    global $config;

    if (!$config['Security']['crypt_method']) {
        return false;
    }

    if ($config['Security']['crypt_method'] == 'G') {
        putenv("GNUPGHOME=".$config['Security']['gpg_home_dir']);

        $gpg_prog = func_shellquote($config['Security']['gpg_prog']);
        $gpg_key = $config['Security']['gpg_key'];

        @exec($gpg_prog." --batch --yes --delete-key '$gpg_key'");
    }
    else {
        putenv("PGPPATH=".$config['Security']['pgp_home_dir']);
        putenv("PGPHOME=".$config['Security']['pgp_home_dir']);

        $pgp_prog = func_shellquote($config['Security']['pgp_prog']);
        $pgp_key = $config['Security']['pgp_key'];

        if ($config['Security']['use_pgp6'] == 'Y') {
            @exec($pgp_prog." -kr +force +batchmode '$pgp_key'");
        }
        else {
            @exec($pgp_prog." -kr +force '$pgp_key'");
        }
    }
}

function func_pgp_add_key()
{
    global $config;

    if (!$config['Security']['crypt_method']) {
        return false;
    }

    if ($config['Security']['crypt_method'] == 'G') {
        putenv("GNUPGHOME=".$config['Security']['gpg_home_dir']);

        $gpg_prog = func_shellquote($config['Security']['gpg_prog']);
        $gpg_key = $config['Security']['gpg_key'];

        $fn = func_temp_store($config['Security']['gpg_public_key']);
        func_chmod_file($fn);

        @exec($gpg_prog.' --batch --yes --import '.func_shellquote($fn));
    }
    else {
        putenv("PGPPATH=".$config['Security']['pgp_home_dir']);
        putenv("PGPHOME=".$config['Security']['pgp_home_dir']);

        $fn = func_temp_store( $config['Security']['pgp_public_key']);

        $pgp_prog = func_shellquote($config['Security']['pgp_prog']);
        $pgp_key = $config['Security']['pgp_key'];

        $ftmp = func_temp_store('');
        if ($config['Security']['use_pgp6'] == 'Y') {
            @exec($pgp_prog.' +batchmode -ka '.func_shellquote($fn).' 2> '.func_shellquote($ftmp));
            @exec($pgp_prog.' +batchmode -ks "'.$pgp_key.'"');
        }
        else {
            @exec($pgp_prog.' -ka +force +batchmode '.func_shellquote($fn).' 2> '.func_shellquote($ftmp));
            @exec($pgp_prog.' +batchmode -ks "'.$pgp_key.'"');
        }

        @unlink($ftmp);
    }

    @unlink($fn);
}

/**
 * This function checks if email is valid
 */
function func_check_email($email)
{

    $email_regular_expression = func_email_validation_regexp();

    return preg_match('/'.$email_regular_expression.'/Di', stripslashes($email));
}

/**
 * Search images in  message body and return message body and images array
 */
function func_attach_images($message)
{
    global $http_location, $xcart_web_dir, $smarty_skin_dir, $xcart_dir, $current_location, $smarty, $xcart_http_host;

    // Get images location
    $hash = array();
    if (preg_match_all("/\ssrc=['\"]?([^\s'\">]+)['\">\s]/SsUi", $message, $preg))
        $hash = $preg[1];

    if (empty($hash))
        return array($message, array());

    // Get images data
    $names = array();
    $images = array();

    $xcart_web_skin_dir = $xcart_web_dir . $smarty_skin_dir;

    foreach ($hash as $v) {

        $orig_name     = $v;
        $parse         = @parse_url($v);
        $data         = '';
        $file_path     = '';

        if (empty($parse['scheme'])) {

            // Web-path without domain name
            $v = str_replace($xcart_web_skin_dir . '/', '', $parse['path']);

            $file_path = $xcart_dir . $smarty_skin_dir . '/' . str_replace('/', XC_DS, $v);

            $v = "http://" . $xcart_http_host . $xcart_web_skin_dir . '/' . $v;

            if (!empty($parse['query']))
                $v .= "?" . $parse['query'];

        } elseif (strpos($v, $current_location) === 0) {

            // Web-path with domain name
            $file_path = $xcart_dir . str_replace('/', XC_DS, substr($v, strlen($current_location)));
        }

        if (
            !empty($file_path)
            && strpos($file_path, '.php') === false
            && strpos($file_path, '.asp') === false
        ) {
            // Get image content as local file
            if (
                file_exists($file_path)
                && is_readable($file_path)
            ) {
                $fp = func_fopen($file_path, 'rb', true);

                if ($fp) {
                    if (filesize($file_path) > 0)
                        $data = fread($fp, filesize($file_path));
                    fclose($fp);
                }

            } else {
                continue;
            }

        }

        if (!empty($images[$v])) {
            continue;
        }

        $tmp = array('name' => basename($v), 'url' => $v, 'data' => $data);
        if ($names[$tmp['name']]) {
            $cnt = 1;
            $name = $tmp['name'];
            while ($names[$tmp['name']]) {
                $tmp['name'] = $name.$cnt++;
            }
        }

        $names[$tmp['name']] = true;
        if (empty($tmp['data'])) {

            // Get image content as URL
            if ($fp = func_fopen($tmp['url'], "rb")) {
                do {
                    $tmpdata = fread($fp, 8192);
                    if (strlen($tmpdata) == 0) {
                        break;
                    }
                    $tmp['data'] .= $tmpdata;
                } while (true);

                fclose($fp);

            } else {
                continue;
            }
        }

        list($tmp1, $tmp2, $tmp3, $tmp['type']) = func_get_image_size(empty($data) ? $tmp['url'] : $file_path);
        if (empty($tmp['type']))
            continue;

        $message = preg_replace("/(['\"\(])".preg_quote($orig_name, "/")."(['\"\)])/Ss", "\\1cid:".$tmp['name']."\\2", $message);
        $images[$tmp['url']] = $tmp;
    }

    return array($message, $images);
}

?>
