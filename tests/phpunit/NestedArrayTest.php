<?php
/**
 * This file is part of the Composer Merge plugin.
 *
 * Copyright (C) 2015 Bryan Davis, Wikimedia Foundation, and contributors
 *
 * This software may be modified and distributed under the terms of the MIT
 * license. See the LICENSE file for details.
 */

namespace Wikimedia\Composer\Merge\V2;

use Composer\Composer;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Wikimedia\Composer\Merge\V2\NestedArray
 */
class NestedArrayTest extends TestCase
{
    /**
     * @covers ::mergeDeep
     * @covers ::mergeDeepArray
     */
    public function testMergeDeepArray()
    {
        $link_options_1 = [
            'fragment' => 'x',
            'attributes' => ['title' => 'X', 'class' => ['a', 'b']],
            'language' => 'en',
        ];
        $link_options_2 = [
            'fragment' => 'y',
            'attributes' => ['title' => 'Y', 'class' => ['c', 'd']],
            'absolute' => true,
        ];
        $expected = [
            'fragment' => 'y',
            'attributes' => [
                'title' => 'Y', 'class' => ['a', 'b', 'c', 'd']
            ],
            'language' => 'en',
            'absolute' => true,
        ];
        $this->assertSame(
            $expected,
            NestedArray::mergeDeepArray(
                [$link_options_1, $link_options_2]
            ),
            'NestedArray::mergeDeepArray() returned a properly merged array.'
        );
        // Test wrapper function, NestedArray::mergeDeep().
        $this->assertSame(
            $expected,
            NestedArray::mergeDeep($link_options_1, $link_options_2),
            'NestedArray::mergeDeep() returned a properly merged array.'
        );
    }

    /**
     * Tests that arrays with implicit keys are appended, not merged.
     *
     * @covers ::mergeDeepArray
     */
    public function testMergeImplicitKeys()
    {
        $a = [
            'subkey' => ['X', 'Y'],
        ];
        $b = [
            'subkey' => ['X'],
        ];
        // Drupal core behavior.
        $expected = [
            'subkey' => ['X', 'Y', 'X'],
        ];
        $actual = NestedArray::mergeDeepArray([$a, $b]);
        $this->assertSame(
            $expected,
            $actual,
            'mergeDeepArray creates new numeric keys in the implicit sequence.'
        );
    }

    /**
     * Tests that even with explicit keys, values are appended, not merged.
     *
     * @covers ::mergeDeepArray
     */
    public function testMergeExplicitKeys()
    {
        $a = [
            'subkey' => [
                0 => 'A',
                1 => 'B',
            ],
        ];
        $b = [
            'subkey' => [
                0 => 'C',
                1 => 'D',
            ],
        ];
        // Drupal core behavior.
        $expected = [
            'subkey' => [
                0 => 'A',
                1 => 'B',
                2 => 'C',
                3 => 'D',
            ],
        ];
        $actual = NestedArray::mergeDeepArray([$a, $b]);
        $this->assertSame(
            $expected,
            $actual,
            'mergeDeepArray creates new numeric keys in the explicit sequence.'
        );
    }

    /**
     * Tests that array keys values on the first array are ignored when merging.
     *
     * Even if the initial ordering would place the data from the second array
     * before those in the first one, they are still appended, and the keys on
     * the first array are deleted and regenerated.
     *
     * @covers ::mergeDeepArray
     */
    public function testMergeOutOfSequenceKeys()
    {
        $a = [
            'subkey' => [
                10 => 'A',
                30 => 'B',
            ],
        ];
        $b = [
            'subkey' => [
                20 => 'C',
                0 => 'D',
            ],
        ];
        // Drupal core behavior.
        $expected = [
            'subkey' => [
                0 => 'A',
                1 => 'B',
                2 => 'C',
                3 => 'D',
            ],
        ];
        $actual = NestedArray::mergeDeepArray([$a, $b]);
        $this->assertSame(
            $expected,
            $actual,
            'mergeDeepArray ignores numeric key order when merging.'
        );
    }
}
// vim:sw=4:ts=4:sts=4:et:
