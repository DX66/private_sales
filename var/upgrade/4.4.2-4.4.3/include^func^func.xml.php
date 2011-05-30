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
 * Functions to work with XML data
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.xml.php,v 1.21.2.1 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * This function parses xml data into array with attributes
 */
function func_xml_parse($data, &$error, $options = array())
{
    static $default_options = array (
        'XML_OPTION_CASE_FOLDING' => 0,
        'XML_OPTION_SKIP_WHITE' => 1
    );

    $data = trim($data);
    $vals = $index = $array = array();
    $parser = xml_parser_create();
    $options = func_array_merge($default_options, $options);

    foreach ($options as $opt=>$val) {
        if (!defined($opt)) continue;

        xml_parser_set_option($parser, constant($opt), $val);
    }

    if (!xml_parse_into_struct($parser, $data, $vals, $index)) {
        $error = array (
            'code' => xml_get_error_code($parser),
            'string' => xml_error_string(xml_get_error_code($parser)),
            'line' => xml_get_current_line_number($parser)
        );
        xml_parser_free($parser);
        return false;
    }

    xml_parser_free($parser);

    $i = 0;

    $tagname = $vals[$i]['tag'];
    if (isset($vals[$i]['attributes'])) {
        $array[$tagname]['@'] = $vals[$i]['attributes'];
    } else {
        $array[$tagname]['@'] = array();
    }

    $array[$tagname]["#"] = _func_xml_make_tree($vals, $i);

    return $array;
}

function _func_xml_make_tree($vals, &$i)
{
    $children = array();

    if (isset($vals[$i]['value'])) {
        array_push($children, $vals[$i]['value']);
    }

    while (++$i < count($vals)) {
        switch ($vals[$i]['type']) {
        case 'open':
            if (isset($vals[$i]['tag'])) {
                $tagname = $vals[$i]['tag'];
            } else {
                $tagname = '';
            }

            if (isset($children[$tagname])) {
                $size = sizeof($children[$tagname]);
            } else {
                $size = 0;
            }

            if (isset($vals[$i]['attributes'])) {
                $children[$tagname][$size]['@'] = $vals[$i]["attributes"];
            }

            $children[$tagname][$size]['#'] = _func_xml_make_tree($vals, $i);
            break;

        case 'cdata':
            array_push($children, $vals[$i]['value']);
            break;

        case 'complete':
            $tagname = $vals[$i]['tag'];

            if (isset($children[$tagname])) {
                $size = sizeof($children[$tagname]);
            } else {
                $size = 0;
            }

            if (isset($vals[$i]['value'])) {
                $children[$tagname][$size]["#"] = $vals[$i]['value'];
            } else {
                $children[$tagname][$size]["#"] = '';
            }

            if (isset($vals[$i]['attributes'])) {
                $children[$tagname][$size]['@'] = $vals[$i]['attributes'];
            }

            break;

        case 'close':
            return $children;
            break;
        }
    }

    return $children;
}

/**
 * This function returns element of array by path to it
 * Returns false when $tag_path cannot be resolved
 */
function & func_array_path(&$array, $tag_path, $strict=false)
{
    if (!is_array($array) || empty($array)) return false;

    if (empty($tag_path)) return $array;

    $path = explode('/',$tag_path);

    $elem =& $array;

    foreach ($path as $key) {
        if (isset($elem[$key])) {
            $tmp_elem =& $elem[$key];
        }
        else {
            if (!$strict && isset($elem['#'][$key])) {
                $tmp_elem =& $elem['#'][$key];
            }
            else if (!$strict && isset($elem[0]['#'][$key])) {
                $tmp_elem =& $elem[0]['#'][$key];
            }
            else {
                // path is not found
                return false;
            }
        }

        unset($elem);
        $elem = $tmp_elem;
        unset($tmp_elem);
    }

    return $elem;
}

/**
 * Covert XML string to hash array
 */
function func_xml2hash($str)
{
    global $xcart_dir;

    $err = false;
    $options = array(
        'XML_OPTION_CASE_FOLDING' => 0,
        'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
    );

    $parsed = func_xml_parse($str, $err, $options);

    if (!empty($parsed)) {
        foreach ($parsed as $k => $v) {
            if (is_array($v['#'])) {
                $is_str = $is_arr = 0;
                foreach ($v['#'] as $subv) {
                    if (is_array($subv)) {
                        $is_array++;
                    } else {
                        $is_str++;
                    }
                }

                if ($is_array > 0 && $is_str > 0) {
                    foreach ($v['#'] as $subk => $subv) {
                        if (!is_array($subv))
                            unset($v['#'][$subk]);
                    }
                }

                if ($is_array > 0) {
                    $parsed[$k] = func_xml2hash_postprocess($v['#']);
                } else {
                    $parsed[$k] = array_pop($v['#']);
                }

            } else  {
                $parsed[$k] = $v['#'];
            }
        }
    }
    else {
        return array();
    }

    return $parsed;

}

/**
 * Covert XML string to hash array: postprocessing subfunction
 */
function func_xml2hash_postprocess($arr)
{
    foreach ($arr as $tname => $t) {
        $arr[$tname] = array_pop($t);
        $arr[$tname] = $arr[$tname]['#'];
        if (is_array($arr[$tname]))
            $arr[$tname] = func_xml2hash_postprocess($arr[$tname]);

    }

    return $arr;
}

/**
 * Convert hash array to XML string
 */
function func_hash2xml($hash, $level = 0, $indent = "\t")
{
    if (!is_array($hash)) {
        return $hash;

    } elseif (empty($hash)) {
        return '';
    }

    $xmk = '';
    foreach($hash as $k => $v) {
        if (is_array($v)) {
            $keys = array_keys($v);
            $range = range(0, count($v)-1);
            if (array_diff($keys, $range)) {
                $xml .= str_repeat($indent, $level)."<$k>".func_hash2xml($v, $level+1, $indent)."</$k>\n";

            } else {
                foreach($v as $subv) {
                    $xml .= str_repeat($indent, $level)."<$k>".func_hash2xml($subv, $level+1, $indent)."</$k>\n";
                }
            }

        } else {
            $xml .= str_repeat($indent, $level)."<$k>".$v."</$k>\n";
        }
    }

    if ($level > 0) {
        $xml = "\n".$xml.str_repeat($indent, ($level - 1));
    }

    return $xml;
}

/**
 * Format XML string
 */
function func_xml_format($xml)
{
    $xml = preg_replace("/>[ \t\n\r]+</", "><", trim($xml));

    $level = -1;
    $i = 0;
    $prev = 0;
    $path = array();
    while(preg_match("/<([\w\d_\?]+)(?: [^>]+)?>/S", substr($xml, $i), $match)) {
        $tn = $match[1];
        $len = strlen($match[0]);
        $i = strpos($xml, $match[0], $i);
        $level++;

        // Detect close-tags
        if ($i - $prev > 0) {
            $ends = substr_count(substr($xml, $prev, $i-$prev), "</");
            if ($ends > 0)
                $level -= $ends;
        }

        // Add indents
        if ($level > 0) {
            $xml = substr($xml, 0, $i).str_repeat("\t", $level).substr($xml, $i);
            $i += $level;
        }

        // Add EOL symbol
        if (
            (
                ($end = strpos(substr($xml, $i+$len), "</$tn>")) !== false &&
                preg_match("/<[\w\d_\?]+(?: [^>]+)?>/S", substr($xml, $i+$len, $end))
            ) ||
            substr($tn, 0, 1) == '?'
        ) {
            $xml = substr($xml, 0, $i+$len)."\n".substr($xml, $i+$len);
            $i++;

            // Add indent for close-tag
            if ($level > 0) {
                $end += $i+$len;
                $xml = substr($xml, 0, $end).str_repeat("\t", $level).substr($xml, $end);
            }
        }

        $i += $len;
        $prev = $i;
    }

    return preg_replace("/(<\/[\w\d_]+>)/", "\\1\n", $xml);
}

/**
 * Escape XML string
 */
function func_xml_escape($str)
{
    return str_replace(
        array("&", "<", ">", '"', "'"),
        array("&#x26;", "&#x3c;", "&#x3e;", "&quot;", "&#39;"),
        $str
    );
}
?>
