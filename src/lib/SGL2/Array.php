<?php

/**
 * Provides array manipulation methods.
 *
 * @package SGL
 * @author  Demian Turner <demian@phpkitchen.com>
 */
class SGL2_Array
{
    /**
     * Strips 'empty' elements from supplied array.
     *
     * 'Empty' can be a null, empty string, false or empty array.
     *
     * @param array $elem
     * @return array
     */
    function removeBlanks($elem)
    {
        if (is_array($elem)) {
            $clean = array_filter($elem);
        } else {
            throw new Exception('array argument expected, got ' . gettype($elem));
        }
        return $clean;
    }

    /**
     * Returns an array with imploded keys.
     *
     * @param string $glue
     * @param array $hash
     * @param string $valwrap
     * @return string
     */
    function implodeWithKeys($glue, $hash, $valwrap='')
    {
        if (is_array($hash) && count($hash)) {
            foreach ($hash as $key => $value) {
                $aResult[] = $key.$glue.$valwrap.$value.$valwrap;
            }
            $ret = implode($glue, $aResult);
        } else {
            $ret = '';
        }
        return $ret;
    }

    /**
     * Merges two arrays and replace existing entrys.
     *
     * Merges two Array like the PHP Function array_merge_recursive.
     * The main difference is that existing keys will be replaced with new values,
     * not combined in a new sub array.
     *
     * Usage:
     *        $newArray = SGL2_Array::mergeReplace($array, $newValues);
     *
     * @access public
     * @param array $array First Array with 'replaceable' Values
     * @param array $newValues Array which will be merged into first one
     * @return array Resulting Array from replacing Process
     */
    function mergeReplace($array, $newValues)
    {
        foreach ($newValues as $key => $value) {
            if (is_array($value)) {
                if (!isset($array[$key])) {
                    $array[$key] = array();
                }
                $array[$key] = self::mergeReplace($array[$key], $value);
            } else {
                if (isset($array[$key]) && is_array($array[$key])) {
                    $array[$key][0] = $value;
                } else {
                    if (isset($array) && !is_array($array)) {
                        $temp = $array;
                        $array = array();
                        $array[0] = $temp;
                    }
                    $array[$key] = $value;
                }
            }
        }
        return $array;
    }
}
?>