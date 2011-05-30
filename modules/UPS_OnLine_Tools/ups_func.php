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
 * Functions for the UPS Developer kit module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: ups_func.php,v 1.31.2.1 2011/01/10 13:12:03 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header('Location: ../'); die('Access denied'); }

/**
 * Get UPS origin location depended on country code
 *
 * @param string $code Country code
 *
 * @return string
 * @see    ____func_see____
 */
function u_get_origin_code($code)
{

    $origin_code = '';

    // EU members (Poland is also EU member, but has different location in $ups_services)

    $eu_members = array('AT', // Austria
                        'BE', // Belgium
                        'BU', // Bulgaria
                        'CY', // Cyprus
                        'CZ', // Czech Republic
                        'DK', // Denmark
                        'EE', // Estonia
                        'FI', // Finland
                        'FR', // France
                        'DE', // Germany
                        'GR', // Greece
                        'HU', // Hungary
                        'IE', // Ireland
                        'IT', // Italy
                        'LV', // Latvia
                        'LT', // Lithuania
                        'LU', // Luxembourg
                        'MT', // Malta
                        'MC', // Monaco
                        'NL', // Netherlands
                        'PT', // Portugal
                        'RO', // Romania
                        'SK', // Slovakia
                        'SI', // Slovenia
                        'ES', // Spain
                        'SE', // Sweden
                        'GB' // United Kingdom
                    );

    if (in_array($code, array('US','CA','PR','MX','PL'))) {

        // Origin is US, Canada, Puerto Rico or Mexico
        $origin_code = $code;

    } elseif (in_array($code, $eu_members)) {

        // Origin is European Union
        $origin_code = 'EU';

    } else {

        // Origin is other countries
        $origin_code = 'OTHER_ORIGINS';
    }

    return $origin_code;
}

/**
 * Generate the unique string
 *
 * @param int $pos    Position
 * @param int $length Length
 *
 * @return string
 * @see    ____func_see____
 */
function u_generate_unique_string($pos, $length)
{

    $str = md5(uniqid(rand()));

    return substr($str, $pos, $length);
}

/**
 * Validate string for using in XML node
 *
 * @param mixed $arg Arguments string/array
 *
 * @return mixed
 * @see    ____func_see____
 */
function func_ups_xml_quote($arg)
{

    if (is_array($arg)) {

        foreach ($arg as $k=>$v) {

            if ($k == 'phone') {

                $arg[$k] = preg_replace('/[^0-9]/', "", $v);

            } elseif (is_string($v)) {

                $arg[$k] = htmlspecialchars($v);

            }
        }

        return $arg;

    } elseif (is_string($arg)) {

        return htmlspecialchars($arg);

    }
}

/**
 * Send XML-request and process XML-response
 *
 * @param mixed $request Request XML
 * @param mixed $func    Data handler func
 * @param mixed $tool    Service name
 *
 * @return void
 * @see    ____func_see____
 */
function u_process($request, $func, $tool)
{
    global $UPS_url;
    global $ps;
    global $show_XML;

    x_load('http');

    if ($show_XML) {
        $out = $request;
        $out = preg_replace('|<AccessLicenseNumber>.*</AccessLicenseNumber>|i','<AccessLicenseNumber>xxx</AccessLicenseNumber>',$out);
        $out = preg_replace('|<UserId>.*</UserId>|i','<UserId>xxx</UserId>',$out);
        $out = preg_replace('|<Password>.*</Password>|i','<Password>xxx</Password>',$out);
        $out = preg_replace('|<DeveloperLicenseNumber>.*</DeveloperLicenseNumber>|i','<DeveloperLicenseNumber>xxx</DeveloperLicenseNumber>',$out);
        print('<pre>'); print(htmlspecialchars($out)); print('</pre>');

        print('Post to: ' . $UPS_url . $tool);
    }

    $ps         = '';
    $ps['tags'] = array();

    $post = explode("\n", $request);

    list ($a, $result) = func_https_request('POST', $UPS_url . $tool, $post, '', '', 'text/xml');

    if ($show_XML) {
        $out = $result;
        $out = preg_replace('|<AccessLicenseNumber>.*</AccessLicenseNumber>|i','<AccessLicenseNumber>xxx</AccessLicenseNumber>',$out);
        $out = preg_replace('|<UserId>.*</UserId>|i','<UserId>xxx</UserId>',$out);
        $out = preg_replace('|<Password>.*</Password>|i','<Password>xxx</Password>',$out);
        $out = preg_replace('/(>)(<[^\/])/', "\\1\n\\2", $out);
        $out = preg_replace('/(<\/[^>]+>)([^\n])/', "\\1\n\\2", $out);
        print('<pre>'); print(htmlspecialchars($out)); print('</pre>');
    }

    $xml_parser = xml_parser_create('ISO-8859-1');
    xml_parser_set_option($xml_parser, XML_OPTION_TARGET_ENCODING, 'ISO-8859-1');
    xml_set_element_handler($xml_parser, 'u_elem_start', 'u_elem_end');
    xml_set_character_data_handler($xml_parser, $func);
    xml_parse($xml_parser, $result);
    xml_parser_free($xml_parser);

}

function u_elem_start($parser, $name, $attrs)
{
    global $ps;

    $ps['tags'][] = $name;
}

function u_elem_end($parser, $name)
{
    global $ps;

    array_pop($ps['tags']);
}

/**
 * Common code for UPS XML tools
 *
 * @param mixed $parser Parser
 * @param mixed $data   Data
 *
 * @return void
 * @see    ____func_see____
 */
function u_elem_data_base($parser, $data)
{
    global $ps;

    if (count($ps['tags']) == 3) {

        if ($ps['tags'][2] == 'RESPONSESTATUSCODE') {

            $ps['statuscode'] = $data;

        } elseif ($ps['tags'][2] == 'RESPONSESTATUSDESCRIPTION') {

            $ps['statusdesc'] = @$ps['statusdesc'].$data;
        }

    } elseif (count($ps['tags']) == 4) {

        if ($ps['tags'][3] == 'ERRORCODE') {
            $ps['errorcode'] = $data;

        } elseif ($ps['tags'][3] == 'ERRORDESCRIPTION') {

            $ps['errordesc'] = @$ps['errordesc'] . $data;

        }
    }
}

/**
 * XML data handler for Register Request
 *
 * @param mixed $parser Parser
 * @param mixed $data   Data
 *
 * @return void
 * @see    ____func_see____
 */
function u_elem_data_reg($parser, $data)
{
    global $ps;

    if (count($ps['tags']) == 2 && $ps['tags'][1] == 'USERID') {

        $ps['UserId'] = $data;

    } else {

        u_elem_data_base($parser, $data);

    }
}

/**
 * XML data handler for AccessLicenseAgreement (License tool)
 *
 * @param mixed $parser Parser
 * @param mixed $data   Data
 *
 * @return void
 * @see    ____func_see____
 */
function u_elem_data_agree($parser, $data)
{
    global $ps;

    if (
        count($ps['tags']) == 2
        && $ps['tags'][1] == 'ACCESSLICENSETEXT'
    ) {
        if (!isset($ps['licensetext'])) {

            $ps['licensetext'] = $data;

        } else {

            $ps['licensetext'] .= $data;

        }
    } else {

        u_elem_data_base($parser, $data);

    }
}

/**
 * XML data handler for AccessLicense (License tool)
 *
 * @param mixed $parser Parser
 * @param mixed $data   Data
 *
 * @return void
 * @see    ____func_see____
 */
function u_elem_data_accept($parser, $data)
{
    global $ps;

    if (
        count($ps['tags']) == 2
        && $ps['tags'][1] == 'ACCESSLICENSENUMBER'
    ) {

        $ps['licensenum'] = $data;

    } else {

        u_elem_data_base($parser, $data);

    }
}

/**
 * XML data handler for Address Validation
 *
 * @param mixed $parser Parser
 * @param mixed $data   Data
 *
 * @return void
 * @see    ____func_see____
 */
function u_elem_data_av($parser, $data)
{
    global $ps, $rank;

    if ($ps['tags'][1] == 'ADDRESSVALIDATIONRESULT') {

        if ($ps['tags'][2] == 'RANK') {
            $rank = $data;
        }

        if (!empty($rank) && !empty($ps['tags'][2])) {

            if ($ps['tags'][2] == 'ADDRESS') {

                $ps[$rank]['ADDRESS'][$ps['tags'][3]] = $data;

            } else {

                $ps[$rank][$ps['tags'][2]] = $data;

            }
        }

    } else {

        u_elem_data_base($parser, $data);
    }
}

/**
 * Validate address
 *
 * @param mixed $av_data Address data
 *
 * @return void
 * @see    ____func_see____
 */
function func_ups_av_validate($av_data)
{
    global $config, $sql_tbl, $active_modules, $xcart_dir;
    global $av_error, $ps, $smarty;

    /**
     * Get and process UPS settings
     */

    $params = func_query_first ("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='UPS'");

    $ups_parameters = unserialize($params['param00']);

    if (!is_array($ups_parameters)) {
        $ups_parameters['av_status'] = 'N';
    }

    if (
        $ups_parameters['av_status'] == 'Y'
        && $av_data['country'] == 'US'
    ) {

        include $xcart_dir . '/modules/UPS_OnLine_Tools/ups_states.php';

        $av_data['state'] = strtoupper($av_data['state']);

        if (!array_key_exists($av_data['state'], $ups_states)) {
            $av_data['state'] = '';
        }

        $required_quality = $ups_parameters['av_quality'];
        $av_error = false;
    }
    else {
        return;
    }

    x_load('crypt');

    $UPS_username  = text_decrypt(trim($config['UPS_OnLine_Tools']['UPS_username']));
    $UPS_password  = text_decrypt(trim($config['UPS_OnLine_Tools']['UPS_password']));
    $UPS_accesskey = text_decrypt(trim($config['UPS_OnLine_Tools']['UPS_accesskey']));

    /**
     * Prepare the Address Validation request
     */

    $request =<<<EOT
<?xml version="1.0"?>
<AccessRequest>
    <AccessLicenseNumber>$UPS_accesskey</AccessLicenseNumber>
    <UserId>$UPS_username</UserId>
    <Password>$UPS_password</Password>
</AccessRequest>
<?xml version="1.0"?>
<AddressValidationRequest xml:lang="en-US">
    <Request>
        <TransactionReference>
            <CustomerContext>Address validation request</CustomerContext>
            <XpciVersion>1.0001</XpciVersion>
        </TransactionReference>
        <RequestAction>AV</RequestAction>
    </Request>
    <Address>
        <City>{$av_data['city']}</City>
        <StateProvinceCode>{$av_data['state']}</StateProvinceCode>
        <PostalCode>{$av_data['zipcode']}</PostalCode>
    </Address>
</AddressValidationRequest>
EOT;

    /**
     * This function Forms the following variables:
     *   $ps['RANK']
     *   $ps['QUALITY']
     *   $ps['ADDRESS.CITY']
     *   $ps['ADDRESS.STATEPROVINCECODE']
     *   $ps['ADDRESS.POSTALCODE']
     *   $ps['PostalCodeLowEnd']
     *   $ps['PostalCodeHighEnd']
     */

    u_process($request, 'u_elem_data_av', 'AV');

    if (isset($ps) && $ps['statuscode'] != '1') {
        return;
    }

    $quality_factors = array (
        'exact'      => array('min' => 1.00, 'max' => 1.00, 'rank' => 5),
        'very_close' => array('min' => 0.95, 'max' => 0.99, 'rank' => 4),
        'close'      => array('min' => 0.90, 'max' => 0.94, 'rank' => 3),
        'possible'   => array('min' => 0.70, 'max' => 0.89, 'rank' => 2),
        'poor'       => array('min' => 0.00, 'max' => 0.69, 'rank' => 1)
    );

    foreach ($quality_factors as $k => $v) {
        if (
            $ps[1]['QUALITY'] >= $v['min']
            && $ps[1]['QUALITY'] <= $v['max']
        ) {
            $quality = $k;
            break;
        }
    }

    $address_is_valid = ($quality_factors[$quality]['rank'] >= $quality_factors[$required_quality]['rank']);

    if (!$address_is_valid) {

        $index = 0;

        foreach ($ps as $k => $v) {

            if (!is_numeric($k)) {
                continue;
            }

            if ($v['POSTALCODELOWEND'] != $v['POSTALCODEHIGHEND']) {

                $max = intval($v['POSTALCODEHIGHEND']) - ($v['POSTALCODELOWEND']);

                for ($i = 0; $i<=$max; $i++) {
                    $av_result[$index]['city']    = $v['ADDRESS']['CITY'];
                    $av_result[$index]['state']   = $v['ADDRESS']['STATEPROVINCECODE'];
                    $av_result[$index]['zipcode'] = $v['POSTALCODELOWEND'] + $i;
                    $av_result[$index]['zipcode'] = str_pad($av_result[$index]['zipcode'], 5, '0', STR_PAD_LEFT);
                    $index++;
                }

            }
            else {
                $av_result[$index]['city']    = $v['ADDRESS']['CITY'];
                $av_result[$index]['state']   = $v['ADDRESS']['STATEPROVINCECODE'];
                $av_result[$index]['zipcode'] = $v['POSTALCODELOWEND'];
                $index++;
            }
        }

        $av_error = array(
            'result'   => $av_result,
            'params'   => func_prepare_address($av_data),
            'request'  => array(
                'post' => $_POST,
                'get'  => $_GET
            )
        );
    }
}

/**
 * Process choice from the suggestion page
 *
 * @param char $av_suggest Choice:
 *                           Y: apply suggested address
 *                           K: keep current address
 *                           R: re-enter address
 * @param int  $rank         Suggestion choice number
 *
 * @return void
 * @see    ____func_see____
 */
function func_ups_av_process_suggestion($av_suggest = 'Y', $rank = 0)
{
    global $av_error;

    $av_data = array();

    if ($av_suggest == 'Y' && isset($rank)) {
        $av_data['city']    = $av_error['result'][$rank]['city'];
        $av_data['state']   = $av_error['result'][$rank]['state'];
        $av_data['zipcode'] = $av_error['result'][$rank]['zipcode'];
    }

    if (isset($av_error['request'])) {
        $_POST = $av_error['request']['post'];
        $_GET  = $av_error['request']['get'];
    }

    define('AV_PROCESSED', true);
    $av_error = false;

    return $av_data;
}
?>
