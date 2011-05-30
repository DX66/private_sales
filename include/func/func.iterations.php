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
 * Iterations functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.iterations.php,v 1.14.2.3 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

define('X_ITERATION_CODE_PATTERN', '[a-zA-Z0-9_\.]{3,8}');

/**
 * Iteration initialize
 */
function func_init_iteration($code)
{
    global $sql_tbl, $XCARTSESSID;

    if (!is_string($code) || !preg_match('/^' . X_ITERATION_CODE_PATTERN . '$/Ss', $code))
        return false;

    db_query('DELETE FROM ' . $sql_tbl['iterations'] . ' WHERE sessid = "' . $XCARTSESSID . '" AND code = "' . $code . '"');
    func_store_iteration_poses($code, 0);

    return true;
}

/**
 * Add iteration task
 */
function func_add_iteration_row($code, $id, $data = '')
{
    global $sql_tbl, $XCARTSESSID;

    if (!is_string($code) || !preg_match('/^' . X_ITERATION_CODE_PATTERN . '$/Ss', $code) || !is_scalar($id) || strlen((string)$id) > 32 || !is_scalar($data))
        return false;

    db_query('REPLACE INTO ' . $sql_tbl['iterations'] . ' (sessid, code, id, data) VALUES ("' . $XCARTSESSID . '", "' . $code . '", "' . $id . '", "' . $data . '")');

    return true;
}

/**
 * Get iteration length
 */
function func_get_iteration_length($code, $data = '')
{
    global $sql_tbl, $XCARTSESSID;

    if (!is_string($code) || !preg_match('/^' . X_ITERATION_CODE_PATTERN . '$/Ss', $code))
        return false;

    $where_data = ($data == '') ? '' : ' AND data ="'. $data .'"';

    return intval(func_query_first_cell('SELECT COUNT(*) FROM ' . $sql_tbl['iterations'] . ' WHERE sessid = "' . $XCARTSESSID . '" AND code = "' . $code . '"' . $where_data));
}

/**
 * Move iteration pointer to specified position
 */
function func_seek_iteration($code, $offset = 0, $whence = SEEK_SET)
{
    if (!is_string($code) || !preg_match('/^' . X_ITERATION_CODE_PATTERN . '$/Ss', $code) || !is_numeric($offset))
        return false;

    switch ($whence) {
        case SEEK_CUR:
            $new = func_store_iteration_poses($code) + $offset;
            break;

        case SEEK_END:
            $new = func_get_iteration_length($code) + $offset;
            break;

        default:
            $new = $offset;
    }

    return func_store_iteration_poses($code, $new);
}

/**
 * Check iteration end-of-list status
 */
function func_eof_iteration($code)
{
    if (!is_string($code) || !preg_match('/^' . X_ITERATION_CODE_PATTERN . '$/Ss', $code))
        return null;

    return func_store_iteration_poses($code) > func_get_iteration_length($code);
}

/**
 * Get current iteration task and move list pointer to next position
 */
function func_each_iteration($code, &$reason)
{
    global $sql_tbl, $XCARTSESSID;

    $is_eof = func_eof_iteration($code);
    if ($is_eof || is_null($is_eof)) {
        $reason = 'eof';
        return false;
    }

    $limit = func_local_iteration_limit($code);
    if ($limit !== false && $limit < 1) {
        $reason = 'limit';
        return false;
    }

    if (!func_check_sysres()) {
        $reason = 'res';
        return false;
    }

    $pos = func_store_iteration_poses($code);
    $data = func_query_first('SELECT id, data FROM ' . $sql_tbl['iterations'] . ' WHERE sessid = "' . $XCARTSESSID . '" AND code = "' . $code . '" ORDER BY sessid, code, id LIMIT ' . $pos . ', 1');
    func_store_iteration_poses($code, $pos + 1);

    if ($limit !== false) {
        func_local_iteration_limit($code, $limit - 1);
    }

    return $data;
}

/**
 * Reset iteration pointer
 */
function func_reset_iteration($code)
{
    if (!is_string($code) || !preg_match('/^' . X_ITERATION_CODE_PATTERN . '$/Ss', $code))
        return false;

    func_store_iteration_poses($code, 0);

    return true;
}

/**
 * Get/Set local iteration limit
 */
function func_local_iteration_limit($code, $limit = false)
{
    static $limits = array();

    if (!is_string($code) || !preg_match('/^' . X_ITERATION_CODE_PATTERN . '$/Ss', $code))
        return false;

    if (is_int($limit)) {
        $limit = max($limit, 0);
        $limits[$code] = $limit;
    }

    return isset($limits[$code]) ? $limits[$code] : false;
}

/**
 * Display dot symbol with specified ratio
 */
function func_tick_iteration($code, $ratio = 1)
{
    if (!is_string($code) || !preg_match('/^' . X_ITERATION_CODE_PATTERN . '$/Ss', $code) || !is_int($ratio))
        return false;

    $ratio = max(0, $ratio);
    if ($ratio > 0 && (func_store_iteration_poses($code) + 1) % $ratio == 0) {
        func_flush('. ');
    }

    return true;
}

/**
 * Service function: store iterations pointers
 */
function func_store_iteration_poses($code, $pos = false)
{
    static $poses = array();

    if (!isset($poses[$code]))
        $poses[$code] = 0;

    if ($pos !== false)
        $poses[$code] = $pos;

    return $poses[$code];
}

function func_get_iteration_data($code, $id)
{
    global $sql_tbl, $XCARTSESSID;

    return func_query_first_cell('SELECT data FROM ' . $sql_tbl['iterations'] . ' WHERE sessid = "' . $XCARTSESSID . '" AND code = "' . $code . '" AND id = "'. $id .'"');
}
?>
