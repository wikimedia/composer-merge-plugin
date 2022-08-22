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

use Composer\Package\BasePackage;
use Composer\Package\Link;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \Wikimedia\Composer\Merge\V2\StabilityFlags
 */
class StabilityFlagsTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @dataProvider provideExplicitStability
     */
    public function testExplicitStability($version, $expect)
    {
        $fixture = new StabilityFlags();
        $got = $fixture->extractAll([
            'test' => $this->makeLink($version)->reveal(),
        ]);
        $this->assertSame($expect, $got['test']);
    }

    public function provideExplicitStability()
    {
        return [
            '@dev' => ['1.0@dev', BasePackage::STABILITY_DEV],
            'dev-' => ['dev-master', BasePackage::STABILITY_DEV],
            '-dev' => ['dev-master#2eb0c09', BasePackage::STABILITY_DEV],
            '@alpha' => ['1.0@alpha', BasePackage::STABILITY_ALPHA],
            '@beta' => ['1.0@beta', BasePackage::STABILITY_BETA],
            '@RC' => ['1.0@RC', BasePackage::STABILITY_RC],
            '@stable' => ['1.0@stable', BasePackage::STABILITY_STABLE],
            '-dev & stable' => [
                '1.0-dev as 1.0.0, 2.0', BasePackage::STABILITY_DEV
            ],
            '@dev | stable' => [
                '1.0@dev || 2.0', BasePackage::STABILITY_DEV
            ],
            '@rc | @beta' => [
                '1.0@rc || 2.0@beta', BasePackage::STABILITY_BETA
            ],
        ];
    }

    /**
     * @dataProvider provideLowestWins
     */
    public function testLowestWins($version, $default, $expect)
    {
        $fixture = new StabilityFlags([
            'test' => $default,
        ]);
        $got = $fixture->extractAll([
            'test' => $this->makeLink($version)->reveal(),
        ]);
        $this->assertSame($expect, $got['test']);
    }

    public function provideLowestWins()
    {
        return [
            [
                '1.0@RC',
                BasePackage::STABILITY_BETA,
                BasePackage::STABILITY_BETA
            ],
            [
                '1.0@dev',
                BasePackage::STABILITY_BETA,
                BasePackage::STABILITY_DEV
            ],
        ];
    }

    protected function makeLink($version)
    {
        $link = $this->prophesize(Link::class);
        $link->getPrettyConstraint()->willReturn($version)->shouldBeCalled();
        return $link;
    }
}
// vim:sw=4:ts=4:sts=4:et:
