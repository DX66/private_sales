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
 * U.S.P.S module (test)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: usps_test.php,v 1.16.2.1 2011/01/10 13:12:01 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) die("ERROR: Can not initiate application! Please check configuration.");

x_load('xml','http');

// USPS user id (currently in test mode)
$user_id = $config['Shipping_Label_Generator']['usps_userid'];
// USPS test secure server url
$server =  "https://secure.shippingapis.com/ShippingAPITest.dll";

// Data array for labels generation
$usps_methods = array(
    'ExpressMail' => array(
        'api' => 'ExpressMailLabel',
        'xml_head' => 'ExpressMailLabelRequest',
        'xml_requests' => array(
            '1' => array(
                'xml_request' => '',
                'file_type' => 'txt',
                'ignore' => '80040320'
            ),
            '2' => array(
                'xml_request' => '',
                'file_type' => 'pdf',
                'ignore' => '80040320'
            )
        )
    ),
    'DeliveryConfirmation' => array    (
        'api' => 'DeliveryConfirmationV3',
        'xml_head' => 'DeliveryConfirmationV3.0',
        'xml_requests' => array(
            '1' => array(
                'xml_request' => '',
                'file_type' => 'tiff',
            ),
            '2' => array(
                'xml_request' => '',
                'file_type' => 'tiff'
            )
        )
    )
);

/**
 * Test requests
 */
$usps_methods['ExpressMail']['xml_requests']['1']['xml_request'] =<<<EOT
<ExpressMailLabelRequest USERID="$user_id">
<Option/>
<EMCAAccount/>
<EMCAPassword/>
<ImageParameters/>
<FromFirstName></FromFirstName>
<FromLastName></FromLastName>
<FromFirm>XYZ Corporation</FromFirm>
<FromAddress1></FromAddress1>
<FromAddress2>1234 ETAILER DR.</FromAddress2>
<FromCity>Los Angeles</FromCity>
<FromState>CA</FromState>
<FromZip5>90052</FromZip5>
<FromZip4>1234</FromZip4>
<FromPhone></FromPhone>
<ToFirstName>LINDA</ToFirstName>
<ToLastName>SHOPPER</ToLastName>
<ToFirm/>
<ToAddress1>APT. 3C</ToAddress1>
<ToAddress2>100 MAIN ST.</ToAddress2>
<ToCity>NEW YORK</ToCity>
<ToState>NY</ToState>
<ToZip5>10010</ToZip5>
<ToZip4>1234</ToZip4>
<ToPhone></ToPhone>
<WeightInOunces>37</WeightInOunces>
<FlatRate></FlatRate>
<WaiverOfSignature>TRUE</WaiverOfSignature>
<NoHoliday>TRUE</NoHoliday>
<POZipCode>90210</POZipCode>
<ImageType>NONE</ImageType>
</ExpressMailLabelRequest>
EOT;

$usps_methods['ExpressMail']['xml_requests']['2']['xml_request'] =<<<EOT
<ExpressMailLabelRequest USERID="$user_id">
<Option/>
<EMCAAccount/>
<EMCAPassword/>
<ImageParameters/>
<FromFirstName></FromFirstName>
<FromLastName></FromLastName>
<FromFirm>XYZ Corporation</FromFirm>
<FromAddress1></FromAddress1>
<FromAddress2>1234 ETAILER DR.</FromAddress2>
<FromCity>Los Angeles</FromCity>
<FromState>CA</FromState>
<FromZip5>90052</FromZip5>
<FromZip4>1234</FromZip4>
<FromPhone></FromPhone>
<ToFirstName>LINDA</ToFirstName>
<ToLastName>SHOPPER</ToLastName>
<ToFirm/>
<ToAddress1>APT. 3C</ToAddress1>
<ToAddress2>100 MAIN ST.</ToAddress2>
<ToCity>NEW YORK</ToCity>
<ToState>NY</ToState>
<ToZip5>10010</ToZip5>
<ToZip4>1234</ToZip4>
<ToPhone></ToPhone>
<WeightInOunces/>
<FlatRate>TRUE</FlatRate>
<NoWeekend>TRUE</NoWeekend>
<POZipCode/>
<ImageType>PDF</ImageType>
</ExpressMailLabelRequest>
EOT;

$usps_methods['DeliveryConfirmation']['xml_requests']['1']['xml_request'] =<<<EOT
<DeliveryConfirmationV3.0Request USERID="$user_id">
<Option>1</Option>
<ImageParameters />
<FromName>John Smith</FromName>
<FromFirm />
<FromAddress1 />
<FromAddress2>475 L'Enfant Plaza, SW</FromAddress2>
<FromCity>Washington</FromCity>
<FromState>DC</FromState>
<FromZip5>20260</FromZip5>
<FromZip4 />
<ToName>Joe Customer</ToName>
<ToFirm />
<ToAddress1>STE 201</ToAddress1>
<ToAddress2>6060 PRIMACY PKWY</ToAddress2>
<ToCity>MEMPHIS</ToCity>
<ToState>TN</ToState>
<ToZip5 />
<ToZip4 />
<WeightInOunces>2</WeightInOunces>
<ServiceType>Priority</ServiceType>
<POZipCode />
<ImageType>TIF</ImageType>
<LabelDate />
</DeliveryConfirmationV3.0Request>
EOT;

$usps_methods['DeliveryConfirmation']['xml_requests']['2']['xml_request'] =<<<EOT
<DeliveryConfirmationV3.0Request USERID="$user_id">
<Option>1</Option>
<ImageParameters />
<FromName>John Smith</FromName>
<FromFirm>U.S. Postal Headquarters</FromFirm>
<FromAddress1 />
<FromAddress2>475 L'Enfant Plaza, SW</FromAddress2>
<FromCity>Washington</FromCity>
<FromState>DC</FromState>
<FromZip5>20260</FromZip5>
<FromZip4>0004</FromZip4>
<ToName>Joe Customer</ToName>
<ToFirm>U.S. Postal Service NCSC</ToFirm>
<ToAddress1>STE 201</ToAddress1>
<ToAddress2>6060 PRIMACY PKWY</ToAddress2>
<ToCity>MEMPHIS</ToCity>
<ToState>TN</ToState>
<ToZip5>38119</ToZip5>
<ToZip4>5718</ToZip4>
<WeightInOunces>2</WeightInOunces>
<ServiceType>Priority</ServiceType>
<POZipCode>20260</POZipCode>
<ImageType>TIF</ImageType>
<LabelDate>07/08/2004</LabelDate>
<CustomerRefNo>A45-3928</CustomerRefNo>
<AddressServiceRequested>TRUE</AddressServiceRequested>
</DeliveryConfirmationV3.0Request>
EOT;

// Delete previous existing test labels
func_rm_dir($xcart_dir.'/var/tmp/usps_test_labels/', true);

$labels = 0;
$error = array();

$_log = '';

foreach ($usps_methods as $method=>$data) {

    foreach ($data['xml_requests'] as $xml_id => $xml_data) {

        $_log .= "************************************************************************************************\n\n";
        $_log .= "[$method]\n\n";
        $_log .= "[REQUEST $xml_id]:\n\n".$xml_data['xml_request']."\n\n";

        $request = $server."?API=".$data['api']."&XML=".urlencode($xml_data['xml_request']);

        $_log .= "[SEND REQUEST TO URL]:\n\n".$request."\n\n";

        // Sending secure GET request to USPS (for first-type image)
        list($header, $return) = func_https_request('GET', $request);

        $_log .= "[RESPONSE]:\n\n".$header."\n\n".$return."\n\n";

        // Parcing first USPS response
        $response = func_usps_parse_result($return, $data['xml_head'], $xml_data['file_type']);

        if ($response['error'] && $response['error_code'] != $xml_data["ignore"]) {
            $error[$method." #".$xml_id] = $response['data'];
        }

        if ($response['error']) {
            $_log .= "[ERROR]:\n\n".$method." ".$xml_id." = ".$response['data']."\n\n";
        }
        else {
            // Saving labels or messages
            func_usps_save_response($response, $method, $xml_id);
        }

    }
}

if (defined('USPS_DEBUG'))
    x_log_add('usps', $_log);

x_session_register('status');
x_session_register('error');

if (!empty($error)) {
    $status = 'E'; // Error
} else {
    $status = 'S'; // Success
}

?>
