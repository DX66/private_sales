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
 * Functions for CC type detecting
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_detect.php,v 1.20.2.1 2011/01/10 13:11:48 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

function is_visa($num)
{
    $first4 = 0+substr($num,0,4);
    return ($first4>=4000 && $first4<=4999);
}

function is_mc($num)
{
    $first4 = 0+substr($num,0,4);
    return ($first4>=5100 && $first4<=5999);
}

function is_amex($num)
{
    $first4 = 0+substr($num,0,4);
    return (($first4>=3400 && $first4<=3499) || ($first4>=3700 && $first4<=3799));
}

function is_diners($num)
{
    $first4 = 0+substr($num,0,4);
    return (($first4>=3000 && $first4<=3059) || ($first4>=3600 && $first4<=3699) || ($first4>=3800 && $first4<=3889));
}

function is_dc($num)
{
    $first4 = 0+substr($num,0,4);
    return ($first4==6011);
}

function is_jcb($num)
{
    $first4 = 0+substr($num,0,4);
    return ($first4>=3528 && $first4<=3589);
}

function is_test($num,$rules)
{
    $result = false;
    $num = trim($num);
    for ($ndx=0; $ndx<count($rules); ++$ndx) {
        list($hiPrefix, $loPrefix, $valLength, $issueLength, $startDateLength) = explode(",", $rules[$ndx]);
        $prefix = substr($num,0,strlen($hiPrefix));

        if ($prefix>=$hiPrefix && $prefix<=$loPrefix && strlen($num)==$valLength) {
            $result = true;
            break;
        }
    }
    return $result;
}

function is_switch($num)
{
    $rules = array("490302,490309,18,1","490335,490339,18,1","491101,491102,16,1","491174,491182,18,1","493600,493699,19,1","564182,564182,16,2","633300,633300,16,0","633301,633301,19,1","633302,633349,16,0","675900,675900,16,0","675901,675901,19,1","675902,675904,16,0","675905,675905,19,1","675906,675917,16,0","675918,675918,19,1","675919,675937,16,0","675938,675940,18,1","675941,675949,16,0","675950,675962,19,1","675963,675997,16,0","675998,675998,19,1","675999,675999,16,0");

    return is_test($num,$rules);
}

function is_solo($num)
{
    $rules = array("633450,633453,16,0","633454,633457,16,0","633458,633460,16,0","633461,633461,18,1","633462,633472,16,0","633473,633473,18,1","633474,633475,16,0","633476,633476,19,1","633477,633477,16,0","633478,633478,18,1","633479,633480,16,0","633481,633481,19,1","633482,633489,16,0","633490,633493,16,1","633494,633494,18,1","633495,633497,16,2","633498,633498,19,1","633499,633499,18,1","676700,676700,16,0","676701,676701,19,1","676702,676702,16,0","676703,676703,18,1","676704,676704,16,0","676705,676705,19,1","676706,676707,16,2","676708,676711,16,0","676712,676715,16,0","676716,676717,16,0","676718,676718,19,1","676719,676739,16,0","676740,676740,18,1","676741,676749,16,0","676750,676762,19,1","676763,676769,16,0","676770,676770,19,1","676771,676773,16,0","676774,676774,18,1","676775,676778,16,0","676779,676779,18,1","676780,676781,16,0","676782,676782,18,1","676783,676794,16,0","676795,676795,18,1","676796,676797,16,0","676798,676798,19,1","676799,676799,16,0");

    return is_test($num,$rules);
}

function is_delta($num)
{
    return false;
}

?>
