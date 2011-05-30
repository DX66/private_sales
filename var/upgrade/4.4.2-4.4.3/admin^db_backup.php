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
 * Database backup script
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: db_backup.php,v 1.85.2.2 2011/01/10 13:11:45 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

func_set_time_limit(86400);

x_load('files');
func_set_memory_limit('64M');

$location[] = array(func_get_langvar_by_name('lbl_db_backup_restore'), '');

define('OVERRIDE_NUM_FIELD', false);

$sqldump_file = $var_dirs['tmp'].'/xcartdump.sql';
$file_exists = file_exists($sqldump_file);

/**
 * While restoring database re-establish connection with mysql server before every Nth table row
 */
$sql_reconnect_count = 100;

/**
 * Make tables backup by selecting N rows per query
 */
$sql_backup_rows = 200;

$smarty->assign('sqldump_file', $sqldump_file);
$smarty->assign('file_exists', $file_exists);

$log_prefix = "Login: $login\nIP: $REMOTE_ADDR\nOperation: $mode\n----\n";

x_session_register('backup_errors');

/**
 * Check post_max_size exceeding
 */
func_check_uploaded_files_sizes('userfile', 523);

/**
 * Dump database code
 */
if($REQUEST_METHOD=="POST" && $mode=="backup") {

    require $xcart_dir.'/include/safe_mode.php';

    // This function returns dump of the table

    function dumpTableContent($table,$schema,$fd) {
        global $sql_backup_rows;

        if ($fd===false) {
            print "$schema\n\n";
        }
        else {
            fwrite($fd, "$schema\n\n");
            echo func_get_langvar_by_name('lbl_dumping_table_n', array('table' => $table),false,true)."<br />\n";
        }
        $rows_cnt=0;
        $current_row=0;

        $count = func_query_first_cell("SELECT count(*) FROM ".$table);
        if ($count < 1)
            return;

        for ($pos=0; $count > 0; $count -= $sql_backup_rows, $pos += $sql_backup_rows) {
            $local_query = "SELECT * FROM $table LIMIT $pos,$sql_backup_rows";
            $result = db_query($local_query);
            if ($result != FALSE) {
                $fields_cnt = mysql_num_fields($result);
                $rows_cnt = mysql_num_rows($result);

                // Checks whether the field is an integer or not
                for ($j = 0; $j < $fields_cnt; $j++) {
                    $field_set[$j] = mysql_field_name($result, $j);
                    $type = mysql_field_type($result, $j);
                    $field_num[$j] = ($type == 'tinyint' || $type == 'smallint' || $type == 'mediumint' || $type == 'int' || $type == 'bigint' || $type == 'timestamp') && (!defined("OVERRIDE_NUM_FIELD") || !constant("OVERRIDE_NUM_FIELD"));
                }

                // Sets the scheme
                $schema_insert = "INSERT INTO $table VALUES (";

                $search = array("\x00", "\x0a", "\x0d", "\x1a"); //\x08\\x09, not required
                $replace = array('0', '\n', '\r', '\Z');
                $current_row  = 0;

                while ($row = mysql_fetch_row($result)) {
                    $current_row++;
                    for ($j = 0; $j < $fields_cnt; $j++) {
                        if (!isset($row[$j])) {
                            $values[]     = 'NULL';
                        }
                        else if ($row[$j] == '0' || $row[$j] != '') {
                            // a number
                            if ($field_num[$j]) {
                                $values[] = $row[$j];
                            }
                            // a string
                            else {
                                $values[] = "'" . str_replace($search, $replace, addslashes($row[$j])) . "'";
                            }
                        }
                        else {
                            $values[]     = "''";
                        } // end if
                    } // end for

                    // Extended inserts case
                    $insert_line  = $schema_insert . implode(', ', $values) . ')';
                    unset($values);

                    // Send the line
                    if ($fd === false) {
                        print $insert_line.";\n";
                        flush();

                    } else {
                        fwrite($fd, $insert_line.";\n");
                        fflush($fd);
                    }

                    // loic1: send a fake header to bypass browser timeout if data
                    //        are bufferized
                } // end while
            } // end if ($result != FALSE)

            db_free_result($result);
            if ($fd !== false)
                func_flush(". ");

        } // for

        if ($fd === false) {
            print "\n";

        } else {
            fwrite($fd,"\n");
            echo "<br />";
        }
    }

    // Function to check for errors after running the query
    function db_backup_check_query($query) {
        global $backup_errors;

        $result = mysql_query($query);

        if ($result)
            return $result;

        $msg .= "Query       : ".$query."\n";
        $msg .= "Error code  : ".mysql_errno()."\n";
        $msg .= "Description : ".mysql_error();

        $backup_errors[] = $msg;
        return false;
    }

    // Flush all delayed queries to data tables
    func_run_delayed_query();

    // Include disabled modules
    $disabled_modules = func_query_column("SELECT module_name FROM $sql_tbl[modules] WHERE active != 'Y'");
    if (!empty($disabled_modules)) {
        foreach($disabled_modules as $mn) {
            if (file_exists($xcart_dir.'/modules/'.$mn.'/config.php'))
                include_once $xcart_dir.'/modules/'.$mn.'/config.php';

        }
    }

    // Prepare the tables list
    $x_tables = array();
    if ($_tables = db_backup_check_query('SHOW TABLES')) {
        while ($_table = db_fetch_row($_tables)) {
            $_table = $_table[0];
            if (constant('X_DEF_OS_WINDOWS') && !in_array($_table, $sql_tbl)) {
                foreach ($sql_tbl as $t) {
                    if (strtoupper($t) == strtoupper($_table)) {
                        $_table = $t;
                    }
                }
            }

            if (!in_array($_table, $sql_tbl))
                continue;

            $x_tables[] = $_table;
        }
    }
    db_free_result($_tables);

    // Check X-Cart tables for errors

    $backup_errors = array();
    foreach($x_tables as $t) {
        if (
            db_backup_check_query("SHOW FIELDS FROM ".$t) &&
            db_backup_check_query("SHOW KEYS FROM ".$t)

        )
            $good_tables[] = $t;
    }

    if (!empty($backup_errors) && !$_POST['force_db_backup']) {
        $top_message = array(
            'type' => 'W',
            'content' => func_get_langvar_by_name('msg_db_backup_sql_error')
        );
        func_header_location("db_backup.php?err=sql");
    }

    // Perform the backup

    $destination = 'browser';
    if ($_POST['write_to_file']) {
        if ($fd = func_fopen($sqldump_file, 'w', true)) {
            $destination = 'file';
        } else {
            $top_message['type'] = 'W';
            $top_message['content'] = func_get_langvar_by_name('txt_the_directory_is_not_writable', array('X' => $var_dirs['tmp']));
            func_header_location('db_backup.php');
        }
    }

    if ($destination == 'browser') {
        header("Content-type: application/force-download");
        header("Content-Disposition: attachment; filename=db_backup.sql");
    }
    else {
        func_display_service_header('lbl_db_backup_in_progress');
        func_flush("<br /><br />");
?>
<script type="text/javascript">
//<![CDATA[
loaded = false;
function refresh()
{
    window.scroll(0, 100000);
    if (loaded == false)
        setTimeout('refresh()', 1000);
}
setTimeout('refresh()', 1000);
//]]>
</script>
<?php
        func_flush();
    }

    foreach ($good_tables as $table) {

        $schema = "CREATE TABLE `$table` (\n";
        $fields = db_query("SHOW FIELDS FROM ".$table);
        $sflag = false;
        while ($field = db_fetch_array($fields)) {
            if ($sflag == true) {
                $schema .= ",\n";
            }

            $schema .= '  `'.$field['Field'].'` '.$field['Type'];
            if ($field['Null'] != 'YES') {
                $schema .= ' NOT NULL';
            }

            if ($field['Default'] !== NULL) {
                $schema .= " default '".$field['Default']."'";
            }

            if (isset($field['Extra'])) {
                $schema .= ' '.$field['Extra'];
            }

            $sflag = true;
        }

        // Add the keys
        $index = array();
        $keys = db_query("SHOW KEYS FROM ".$table);
        if ($keys != FALSE) {
            while ($key = db_fetch_array($keys)) {
                $kname = $key['Key_name'];
                if ($kname == 'PRIMARY') {
                    $kname = "PRIMARY KEY";
                } elseif ($key['Non_unique'] == 0) {
                    $kname = "UNIQUE ".$kname;
                } elseif ((!isset($key['Index_type']) && $key['Comment'] == "FULLTEXT") || (isset($key['Index_type']) && $key['Index_type'] == 'FULLTEXT')) {
                    $kname = "FULLTEXT ".$kname;
                } else {
                    $kname = "KEY ".$kname;
                }

                if (!isset($index[$kname])) {
                    $index[$kname] = array();
                }

                $index[$kname][] = $key['Column_name'];
            }
        }

        foreach ($index as $kname => $columns) {
            $schema .= ",\n  ".$kname." (".implode(",",$columns).")";
        }

        $schema .= "\n) TYPE=MyISAM;";

        dumpTableContent($table,$schema,$destination=="file"?$fd:false);
    }

    func_update_db_backup_generation_date();

    if ($destination == 'file') {
        fclose($fd);
        func_chmod_file($sqldump_file);
        $top_message['content'] = func_get_langvar_by_name('msg_adm_db_backup_success'). " '$sqldump_file'";
        echo "<hr />".func_get_langvar_by_name('lbl_done',false,false,true).'.';
?>
<script type="text/javascript">
//<![CDATA[
loaded = true;
//]]>
</script>
<?php
        func_flush();
        x_log_flag('log_database', 'DATABASE', $log_prefix.func_get_langvar_by_name("msg_adm_db_backup_success",false,false,true));
        func_html_location('db_backup.php',10);

    } else {
        x_log_flag('log_database', 'DATABASE', $log_prefix.func_get_langvar_by_name("lbl_done",false,false,true));
    }

    exit;
}

/**
 * Restore database code
 */
if ($REQUEST_METHOD == 'POST' && $mode == 'restore' && empty($_POST['local_file']) && $_FILES['userfile']['error']) {
    $upload_error_codes = array();
    $upload_error_codes[1] = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
    $upload_error_codes[2] = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form.";
    $upload_error_codes[3] = "The uploaded file was only partially uploaded.";
    $upload_error_codes[4] = "No file was uploaded.";

    $top_message['content'] = func_get_langvar_by_name('msg_adm_err_file_upload')." [".$_FILES['userfile']['error'].": ".$upload_error_codes[$_FILES['userfile']['error']]."]";
    $top_message['type'] = 'E';

    x_log_flag('log_database', 'DATABASE', $log_prefix.$top_message["content"]);
    func_header_location('db_backup.php');
}

if ($REQUEST_METHOD == 'POST' && $mode == 'restore' && (($userfile != 'none' && $userfile != '') || ($_POST['local_file'] && $file_exists))) {

    require $xcart_dir.'/include/safe_mode.php';

    x_log_flag('log_database', 'DATABASE', $log_prefix.'Started');

    $source_file = 'uploaded';
    if ($_POST['local_file']) {
        if ($fd = func_fopen($sqldump_file, 'r', true))
            $source_file = 'local';
    }

    if ($source_file == 'uploaded')
        $userfile = func_move_uploaded_file('userfile');
    else
        $userfile = $sqldump_file;

    $fp = func_fopen($userfile, 'rb', true);
    if ($fp === false) {
        $top_message['content'] = func_get_langvar_by_name('msg_adm_err_sql_file_not_found');
        $top_message['type'] = 'E';
        x_log_flag('log_database', 'DATABASE', $log_prefix.func_get_langvar_by_name("msg_adm_err_sql_file_not_found",false,false,true));
        func_header_location('db_backup.php');
    }

    $command = '';
    func_flush(func_get_langvar_by_name('lbl_please_wait', false, false, true) . "<br />\n");
    $cmdcnt = 0;
    $error = false;
    while (!feof($fp)) {
        $c = fgets($fp, 1500000);
        $c = chop($c);
        $c = preg_replace("/^[ \t]*(#|-- |---*).*/S", '', $c);
        $command .= $c;
        if (preg_match("/^(.+);$/Ss", $command, $match)) {
            $command = $match[1];
            if (preg_match("/^CREATE TABLE\s+`?([\w\d_]+)`?\s/Si", $command, $match)) {
                $table_name = $match[1];
                if ($cmdcnt > 1)
                    echo "<br />\n";

                func_flush(func_get_langvar_by_name('lbl_restoring_table_n', array('table' => $table_name), false, true)."<br />\n");

                db_query("DROP TABLE IF EXISTS `$table_name`");
                $cmdcnt = 0;
            }

            $cmdcnt++;
            if ($sql_reconnect_count > 0 && $cmdcnt % $sql_reconnect_count == 0) {

                // While restoring database re-establish connection
                // with mysql server before every Nth table row

                db_connect($sql_host, $sql_user, $sql_password);
                db_select_db($sql_db) || die("Could not connect to SQL db");
            }

            db_query($command);
            if ($cmdcnt % 20 == 0)
                func_flush('.');

            if ($cmdcnt % 3000 == 0)
                func_flush("<br />\n");

            $myerr = mysql_error ();
            if (!empty($myerr)) {
                $error = true;
                func_flush($myerr);
                break;
            }

            $command = '';
        }
    }

    if ($cmdcnt > 0)
        func_flush("<br />\n");

    fclose($fp);
    if ($source_file == 'uploaded')
        @unlink($userfile);

    $smarty->clear_compiled_tpl(); // language variables may change

    if($error)
        $msg = '<span style="color: red">An SQL error occurred during the database restore operation. Perhaps, your backup file is corrupted or there is a problem on your server.</span>'; // cannot use language variables because the database is likely to be corrupted
    else
        $msg = func_get_langvar_by_name('lbl_db_restored_successfully',false,false,true);

    func_flush("<p><b>$msg</b></p>\n<p><a href=\"db_backup.php\">".func_get_langvar_by_name('lbl_go_back', false, false, true)."</a></p>");
    exit;
}

$smarty->assign('upload_max_filesize', func_convert_to_megabyte(func_upload_max_filesize()));

if ($err == 'sql' && !empty($backup_errors)) {
    $smarty->assign('backup_errors', $backup_errors);
    x_session_unregister('backup_errors');
}

/**
 * Smarty display code goes here
 */
$smarty->assign('main', 'db_backup');

// Assign the current location line
$smarty->assign('location', $location);

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('admin/home.tpl', $smarty);
?>
