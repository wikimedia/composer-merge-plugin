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
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \Wikimedia\Composer\Merge\V2\PluginState
 */
class PluginStateTest extends TestCase
{
    use ProphecyTrait;

    public function testLocked()
    {
        $composer = $this->prophesize(Composer::class)->reveal();
        $fixture = new PluginState($composer);

        $this->assertFalse($fixture->isLocked());
        $this->assertTrue($fixture->forceUpdate());

        $fixture->setLocked(true);
        $this->assertTrue($fixture->isLocked());
        $this->assertFalse($fixture->forceUpdate());
    }

    public function testDumpAutoloader()
    {
        $composer = $this->prophesize(Composer::class)->reveal();
        $fixture = new PluginState($composer);

        $this->assertFalse($fixture->shouldDumpAutoloader());

        $fixture->setDumpAutoloader(true);
        $this->assertTrue($fixture->shouldDumpAutoloader());
    }

    public function testOptimizeAutoloader()
    {
        $composer = $this->prophesize(Composer::class)->reveal();
        $fixture = new PluginState($composer);

        $this->assertFalse($fixture->shouldOptimizeAutoloader());

        $fixture->setOptimizeAutoloader(true);
        $this->assertTrue($fixture->shouldOptimizeAutoloader());
    }
}
// vim:sw=4:ts=4:sts=4:et:
