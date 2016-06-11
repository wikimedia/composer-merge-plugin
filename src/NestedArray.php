<?php
/**
 * This file is part of the Composer Merge plugin.
 *
 * Copyright (C) 2015 Bryan Davis, Wikimedia Foundation, and contributors
 *
 * This software may be modified and distributed under the terms of the MIT
 * license. See the LICENSE file for details.
 */

namespace Wikimedia\Composer;

/**
 * Helper class to merge arrays in a deep fashion.
 *
 * @author Bryan Davis <bd808@bd808.com>
 */
class NestedArray
{
    /**
     * Merges multiple arrays, recursively, and returns the merged array.
     *
     * This function is similar to PHP's array_merge_recursive() function, but it
     * handles non-array values differently. When merging values that are not both
     * arrays, the latter value replaces the former rather than merging with it.
     *
     * Example:
     * @code
     * $link_options_1 = array('fragment' => 'x', 'attributes' => array('title' => t('X'), 'class' => array('a', 'b')));
     * $link_options_2 = array('fragment' => 'y', 'attributes' => array('title' => t('Y'), 'class' => array('c', 'd')));
     *
     * // This results in array(
     * //     'fragment' => array('x', 'y'),
     * //     'attributes' => array('title' => array(t('X'), t('Y')), 'class' => array('a', 'b', 'c', 'd'))
     * // ).
     * $incorrect = array_merge_recursive($link_options_1, $link_options_2);
     *
     * // This results in array(
     * //     'fragment' => 'y',
     * //     'attributes' => array('title' => t('Y'), 'class' => array('a', 'b', 'c', 'd'))
     * // ).
     * $correct = $this->arrayMergeDeep($link_options_1, $link_options_2);
     * @endcode
     *
     * Note: This function was derived from Drupal's drupal_array_merge_deep().
     *
     * @param array ...
     *   Arrays to merge.
     *
     * @return array
     *   The merged array.
     */
    public static function mergeDeep()
    {
        $arrays = func_get_args();
        $result = array();

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                // Renumber integer keys as array_merge_recursive() does. Note that PHP
                // automatically converts array keys that are integer strings (e.g., '1')
                // to integers.
                if (is_integer($key)) {
                    $result[] = $value;
                } elseif (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                    // Recurse when both values are arrays.
                    $result[$key] = self::mergeDeep($result[$key], $value);
                } else {
                    // Otherwise, use the latter value, overriding any previous value.
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }
}
