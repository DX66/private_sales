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
 * Download shipping label.
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: slabel.php,v 1.23.2.1 2011/01/10 13:11:44 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './top.inc.php';

require './init.php';

x_session_register('login_type');

x_session_register('login');

if (
    empty($active_modules['Shipping_Label_Generator'])
    || !in_array(
        $login_type,
        array(
            'A',
            'P',
        )
    )
) {
    func_403(74);
}

x_session_register('slg_ups_orders');

x_session_register('slg_img_orders');

$label_data = null;

$labelid = isset($labelid)
    ? $labelid
    : '';

if (
    $mode == 'ups_labels'
    && !empty($slg_ups_orders)
) {

    // Fetch labels for all UPS methods at Shipping Labels page of x-cart admin/provider area.
    $label_data = func_slg_get_ups_labels();

} elseif (
    $mode == 'img_labels'
    && !empty($slg_img_orders)
) {

    // Fetch data for all orders with graphical labels at Shipping Labels page of x-cart admin/provider area.
    $orders_data = func_slg_get_img_labels_orders_data();

    $smarty->assign('orders', $orders_data);

    func_display('modules/Shipping_Label_Generator/labels.tpl', $smarty);

    exit;

} elseif (
    !empty($orderid)
    && is_numeric($orderid)
) {

    $label_data = func_slg_get_label($orderid, $labelid);
}

if (
    empty($label_data)
    || empty($label_data['label'])
    || empty($label_data['mime_type'])
    || !empty($label_data['error'])
) {
    func_403(72);
}

// Push data
header("Content-Type: " . $label_data['mime_type']);

header(
    'Content-Disposition: attachment; filename="label-'
    . $label_data['orderid']
    . (
        !empty($label_data['labelid'])
            ? '-' . $label_data['labelid']
            : ''
    )
    . '.'
    . preg_replace("/^\w+\//i", '', $label_data['mime_type'])
    . '"'
);

header('Content-Length: ' . strlen($label_data['label']));

echo $label_data['label'];

exit;

?>
