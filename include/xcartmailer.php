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
 * X-Cart SMTP mailer
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: xcartmailer.php,v 1.18.2.1 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

require_once $xcart_dir.'/include/lib/phpmailer/class.smtp.php';

class XcartMailer extends SMTP {
    var $hostname = null;
    var $implemented_auth_methods = array("DIGEST-MD5", "CRAM-MD5", 'LOGIN', 'PLAIN');
    var $server_auth_methods = null;
    var $auth_method = null;

    function XcartMailer($auth_method = null) {
        parent::SMTP();
        $this->auth_method = $auth_method;
    }

    // Sets desired SMTP authentication method to specified value.

    function setAuthMethod($method) {
        $this->auth_method = $method;
    }

    // Returns requested SMTP auth method.

    function getAuthMethod() {
        return $this->auth_method;
    }

    // Handles opening of SMTP connection.

    function Connect($host, $port = 0, $tval = 30) {
        $result = parent::Connect($host, $port, $tval);

        if ($result != true) {
            return false;
        }

        $this->hostname = $host;

        return true;
    }

    // Handles closing of SMTP connection.

    function Close() {
        parent::Close();
        $this->hostname = null;
    }

    // Handles parsing of server response for HELO/EHLO commands.

    function SendHello($hello, $host) {
        $result = parent::SendHello($hello, $host);

        if ($result != true) {
            return false;
        }

        // Detect SMTP AUTH methods supported by mail server.
        if (!empty($this->helo_rply)) {
            $rply = explode("\n", $this->helo_rply);
            if (!empty($rply) && is_array($rply)) {
                foreach ($rply as $str) {
                    $args = trim(substr($str, 4));
                    if (preg_match("/^AUTH /", $args)) {
                        $args = explode(" ", trim(substr($str, 9)));
                        if (!empty($args)) {
                            $this->server_auth_methods = $args;
                            break;
                        }
                    }
                }
            }
        }

        return true;
    }

    // Handles SMTP authentication.

    function Authenticate($username, $password) {
        // Exit if server does not support any SMTP AUTH methods.
        if (empty($this->server_auth_methods)) {
            $this->error = array('error' => "Server does not support SMTP authentication");
            return false;
        }

        // Exit if server does not support SMTP AUTH methods implemented by this class.
        if (!count(array_intersect($this->implemented_auth_methods, $this->server_auth_methods))) {
            $this->error = array('error' => "Server does not support AUTH methods implemented by XcartMailer class. Server methods: " . join(", ", $this->server_auth_methods));
            return false;
        }

        // Exit if server does not support SMTP AUTH methods requested by user.
        if (!empty($this->auth_method) && !in_array($this->auth_method, $this->server_auth_methods)) {
            $this->error = array('error' => "Server does not support requested AUTH method: " . $this->auth_method);
            return false;
        }

        // Determine the best SMTP AUTH method supported by SMTP server if requested SMTP AUTH method is empty.
        if (empty($this->auth_method)) {
            foreach ($this->implemented_auth_methods as $m) {
                if (in_array($m, $this->server_auth_methods)) {
                    $this->auth_method = $m;
                    break;
                }
            }
        }

        // Delegate SMTP authentication to helper functions
        switch ($this->auth_method) {
        case 'LOGIN':
            $result = $this->auth_login($username, $password);
            break;
        case 'PLAIN':
            $result = $this->auth_plain($username, $password);
            break;
        case 'CRAM-MD5':
            $result = $this->auth_cram_md5($username, $password);
            break;
        case 'DIGEST-MD5':
            $result = $this->auth_digest_md5($username, $password);
            break;
        default:
            $result = false;
            $this->error = array('error' => "Caller requested not implemented auth method: " . $this->auth_method);
        }

        return $result;
    }

    // SMTP AUTH LOGIN method

    function auth_login($username, $password) {
        // Start authentication.
        fputs($this->smtp_conn,"AUTH LOGIN" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if ($code != 334) {
            // Error: already authenticated.
            if ($code == 503) {
                return true;
            }

            $this->error =
            array('error' => "AUTH not accepted from server",
                'smtp_code' => $code,
                'smtp_msg' => substr($rply,4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error['error'] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }

        // Send encoded username
        fputs($this->smtp_conn, base64_encode($username) . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if ($code != 334) {
            $this->error =
            array('error' => "Username not accepted from server",
                'smtp_code' => $code,
                'smtp_msg' => substr($rply,4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error['error'] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }

        // Send encoded password
        fputs($this->smtp_conn, base64_encode($password) . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if ($code != 235) {
            $this->error =
            array('error' => "Password not accepted from server",
                'smtp_code' => $code,
                'smtp_msg' => substr($rply,4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error['error'] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }

        return true;
    }

    // SMTP AUTH PLAIN method

    function auth_plain($username, $password) {
        // Start authentication
        fputs($this->smtp_conn,"AUTH PLAIN" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if ($code != 334) {
            // Error: already authenticated.
            if ($code == 503) {
                return true;
            }

            $this->error =
            array('error' => "AUTH not accepted from server",
                'smtp_code' => $code,
                'smtp_msg' => substr($rply,4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error['error'] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }

        // Send encoded password
        fputs($this->smtp_conn, base64_encode(chr(0).$username.chr(0).$password) . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if ($code != 235) {
            $this->error =
            array('error' => "Password not accepted from server",
                'smtp_code' => $code,
                'smtp_msg' => substr($rply,4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error['error'] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }

        return true;
    }

    // SMTP AUTH CRAM-MD5 method

    function auth_cram_md5($username, $password) {
        // Start authentication
        fputs($this->smtp_conn,"AUTH CRAM-MD5" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if ($code != 334) {
            // Error: already authenticated.
            if ($code == 503) {
                return true;
            }

            $this->error =
            array('error' => "AUTH not accepted from server",
                'smtp_code' => $code,
                'smtp_msg' => substr($rply,4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error['error'] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }

        // Read challenge from server.
        $challenge = base64_decode(trim(substr($rply, 4)));

        // Send encoded password
        fputs($this->smtp_conn, base64_encode($username.' '.$this->hmac_md5($password, $challenge)) . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if ($code != 235) {
            $this->error =
            array('error' => "Password not accepted from server",
                'smtp_code' => $code,
                'smtp_msg' => substr($rply,4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error['error'] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }

        return true;
    }

    // SMTP AUTH DIGEST-MD5 method

    function auth_digest_md5($username, $password) {
        // Start authentication
        fputs($this->smtp_conn,"AUTH DIGEST-MD5" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if ($code != 334) {
            // Error: already authenticated.
            if ($code == 503) {
                return true;
            }

            $this->error =
            array('error' => "AUTH not accepted from server",
                'smtp_code' => $code,
                'smtp_msg' => substr($rply,4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error['error'] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }

        // Read challenge from server.
        $challenge = base64_decode(trim(substr($rply, 4)));

        // Generate Digest-MD5 response.
        $auth_string = $this->get_digest_md5_response($username, $password, $challenge, $this->hostname, 'smtp');
        if (is_null($auth_string)) {
            $this->error = array('error' => "Failed to generate DIGEST-MD5 response");
            return false;
        }

        // Send Digest-MD5.
        fputs($this->smtp_conn, base64_encode($auth_string) . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if ($code != 334) {
            $this->error =
            array('error' => "Failed second DIGEST-MD5 auth step",
                'smtp_code' => $code,
                'smtp_msg' => substr($rply,4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error['error'] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }

        fputs($this->smtp_conn, '' . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if ($code != 235) {
            $this->error =
            array('error' => "Password not accepted from server",
                'smtp_code' => $code,
                'smtp_msg' => substr($rply,4));
            if ($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error['error'] .
                ": " . $rply . $this->CRLF;
            }
            return false;
        }

        return true;
    }

    function hmac_md5($key, $data) {
        if (strlen($key) > 64) {
            $key = pack('H32', md5($key));
        }

        if (strlen($key) < 64) {
            $key = str_pad($key, 64, chr(0));
        }

        $k_ipad = substr($key, 0, 64) ^ str_repeat(chr(0x36), 64);
        $k_opad = substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64);

        $inner  = pack('H32', md5($k_ipad . $data));
        $outer = md5($k_opad . $inner);

        return $outer;
    }

    function get_digest_md5_response($authcid, $pass, $challenge, $hostname, $service, $authzid = '') {
        $challenge = $this->parse_digest_md5_challenge($challenge);
        $authzid_string = '';
        if ($authzid != '') {
            $authzid_string = ',authzid="' . $authzid . '"';
        }

        if (!empty($challenge)) {
            $cnonce         = $this->get_digest_md5_cnonce();
            $digest_uri     = sprintf('%s/%s', $service, $hostname);
            $response_value = $this->get_digest_md5_response_value($authcid, $pass, $challenge['realm'], $challenge['nonce'], $cnonce, $digest_uri, $authzid);

            if ($challenge['realm']) {
                return sprintf('username="%s",realm="%s"' . $authzid_string  .
                ',nonce="%s",cnonce="%s",nc=00000001,qop=auth,digest-uri="%s",response=%s,maxbuf=%d',
                $authcid, $challenge['realm'], $challenge['nonce'], $cnonce, $digest_uri, $response_value, $challenge['maxbuf']);
            } else {
                return sprintf('username="%s"' . $authzid_string  . ',nonce="%s",cnonce="%s",nc=00000001,qop=auth,digest-uri="%s",response=%s,maxbuf=%d',
                    $authcid, $challenge['nonce'], $cnonce, $digest_uri, $response_value, $challenge['maxbuf']);
            }
        } else {
            return null;
        }
    }

    function parse_digest_md5_challenge($challenge) {
        $tokens = array();
        while (preg_match('/^([a-z-]+)=("[^"]+(?<!\\\)"|[^,]+)/i', $challenge, $matches)) {

            // Ignore these as per rfc2831
            if ($matches[1] == 'opaque' || $matches[1] == 'domain') {
                $challenge = substr($challenge, strlen($matches[0]) + 1);
                continue;
            }

            // Allowed multiple 'realm' and "auth-param"
            if (!empty($tokens[$matches[1]]) && ($matches[1] == 'realm' || $matches[1] == 'auth-param')) {
                if (is_array($tokens[$matches[1]])) {
                    $tokens[$matches[1]][] = preg_replace('/^"(.*)"$/', '\\1', $matches[2]);
                } else {
                    $tokens[$matches[1]] = array($tokens[$matches[1]], preg_replace('/^"(.*)"$/', '\\1', $matches[2]));
                }
                // Any other multiple instance = failure
            } elseif (!empty($tokens[$matches[1]])) {
                $tokens = array();
                break;
            } else {
                $tokens[$matches[1]] = preg_replace('/^"(.*)"$/', '\\1', $matches[2]);
            }

            // Remove the just parsed directive from the challenge
            $challenge = substr($challenge, strlen($matches[0]) + 1);
        }

        // Realm
        if (empty($tokens['realm'])) {
            $tokens['realm'] = '';
        }

        // Maxbuf
        if (empty($tokens['maxbuf'])) {
            $tokens['maxbuf'] = 65536;
        }

        // Required: nonce, algorithm
        if (empty($tokens['nonce']) || empty($tokens['algorithm'])) {
            return array();
        }

        return $tokens;
    }

    function get_digest_md5_response_value($authcid, $pass, $realm, $nonce, $cnonce, $digest_uri, $authzid = '') {
        if ($authzid == '') {
            $A1 = sprintf('%s:%s:%s', pack('H32', md5(sprintf('%s:%s:%s', $authcid, $realm, $pass))), $nonce, $cnonce);
        } else {
            $A1 = sprintf('%s:%s:%s:%s', pack('H32', md5(sprintf('%s:%s:%s', $authcid, $realm, $pass))), $nonce, $cnonce, $authzid);
        }
        $A2 = 'AUTHENTICATE:' . $digest_uri;

        return md5(sprintf('%s:%s:00000001:%s:auth:%s', md5($A1), $nonce, $cnonce, md5($A2)));
    }

    function get_digest_md5_cnonce() {
        $str = '';
        mt_srand((double)microtime()*10000000);
        for ($i = 0; $i < 32; $i++) {
            $str .= chr(mt_rand(0, 255));
        }

        return base64_encode($str);
    }
}

?>
