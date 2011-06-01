<?php

/**
 * Helper functions for the Dx66 company.
 * At the moment only cart timeout functions
 * 
 * @author Adam Maschek <adam.maschek@gmail.com>
 */

if (!defined('XCART_START')) {
    header("Location: ../../");
    die("Access denied");
}

/**
 * Empties the cart of a given user(session).
 * Functionality peeked from session.php
 * 
 * @global arry $sql_tbl Array of sql table names
 * @param string $sessid The session ID to wipe out
 */
function dx66_empty_cart($sessid) {
    global $sql_tbl;

    $sql = "DELETE FROM {$sql_tbl['sessions_data']} WHERE sessid = '" . $sessid . "'";
    db_query($sql);

    func_delete_session_related_data(array($sessid));
}