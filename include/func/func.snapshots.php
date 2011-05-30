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
 * Snapshots subsystem
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.snapshots.php,v 1.24.2.1 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * This function filter the filename
 */
function f_file_filter ($file)
{
    global $smarty;
    $return = false;

    if (!strstr(dirname($file), $smarty->compile_dir) && !preg_match("!md5_[0-9]+log\.php!S", basename($file)))
        $return = true;

    return $return;
}

/**
 * This function generates the MD5 file name by timestamp
 */
function f_get_md5file_name($time)
{
    global $var_dirs;

    return $var_dirs['log'].'/md5_'.$time.'log.php';
}

/**
 * This function generates the list of files in the $dirname directory
 */
function f_get_filelist ($dirname)
{
    global $files_list, $md5file, $smarty;

    $fd = @opendir($dirname);
    while ($file = @readdir($fd)) {
        if (in_array($file, array('.', '..')) || preg_match("!md5_[0-9]+log\.php!S", $file) || strstr($dirname, $smarty->compile_dir))
            continue;
        if (is_dir($dirname.XC_DS.$file))
            f_get_filelist ($dirname.XC_DS.$file);
        else
            $files_list[] = $dirname.XC_DS.$file;
    }
    @closedir($fd);

    return $files_list;
}

/**
 * This function selects option 'snapshots' from xcart_config table
 */
function f_get_snapshots ()
{
    global $sql_tbl;

    $snapshots = func_query_first_cell("SELECT value FROM $sql_tbl[config] WHERE name='snapshots'");
    if (!empty($snapshots))
        $snapshots = unserialize($snapshots);
    if (!is_array($snapshots))
        $snapshots = array();

    return $snapshots;
}

/**
 * This function updates/inserts option 'snapshots' in xcart_config table
 */
function f_update_snapshots ($value)
{
    global $sql_tbl;

    db_query("REPLACE INTO $sql_tbl[config] (name, value) VALUES ('snapshots', '".addslashes(serialize($value))."')");

}

/**
 * This functin processes file (get MD5)
 */
function f_process_file ($file)
{
    global $file_log;

    if ($md5 = func_md5_file($file))
        $file_log[$file] = $md5;
    else
        $file_log[$file] = ($md5 = 'R');

    return $md5;
}

/**
 * This function generates the snapshot and writes it to the file
 */
function func_generate_snapshot($file_to_write, $install=false)
{
    global $xcart_dir, $file_log;

    $src_name = $xcart_dir;

    $return['error'] = 0;

    $file_log = array();
    $unprocessed_files_list = array();

    $files_list = f_get_filelist($src_name);

    if (is_array($files_list)) {

        $unknown_files = 0;
        $counter = 0;

        if ($fc = fopen($file_to_write, "w+")) {
            fwrite($fc, "<?php exit; ?>\n");

            foreach ($files_list as $file) {
                if (is_dir($file))
                    continue;

                if (f_file_filter($file)) {
                    // Process file...
                    $res = f_process_file($file);

                    // Write to the fingerprint file...
                    $fname = preg_replace("/^".preg_quote($src_name,'/').'/', '', $file);
                    fwrite($fc, base64_encode($fname).":".$file_log[$file]."\n");
                    if ($install) // Empty $file_log when installing to avoid memory exceed
                        $file_log = array();

                    if ($res == 'R') {
                        $unknown_files++;
                        $unprocessed_files_list[] = $fname;
                    }

                    $counter++;
                    if ($counter % 50 == 0) {
                        echo '.';
                        if ($counter % 5000 == 0) {
                            echo "<br />\n";
                        }
                        func_flush();
                    }
                }
            }

            fclose($fc);
            func_chmod_file($file_to_write);

            echo "<br />";

            $return['total_files'] = ($install ? $counter : count($file_log));
            $return['unprocessed_files'] = $unknown_files;
            $return['unprocessed_files_list'] = $unprocessed_files_list;
        }
        else {
            $return['error'] = 1;
            $return['errordescr'] = 'snpst_write_file';
        }
    }
    else {
        $return['error'] = 1;
        $return['errordescr'] = 'snpst_no_files';
    }

    return $return;
}

/**
 * This function writes unprocessed files during snapshot generation into log file
 */
function func_snapshot_add_to_log($files)
{
    if (!empty($files) && is_array($files)) {
        $log = "The following files were not included into the system fingerprint:\n";
        foreach ($files as $filename) {
            $log .= $filename."\n";
        }
        x_log_add('SNAPSHOT',$log);
    }
}
?>
