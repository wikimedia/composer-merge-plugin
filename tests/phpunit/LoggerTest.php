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

use Prophecy\Argument;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Wikimedia\Composer\Merge\V2\Logger
 */
class LoggerTest extends TestCase
{

    public function testVeryVerboseDebug()
    {
        $output = [];
        $io = $this->prophesize(\Composer\IO\IOInterface::class);
        $io->isVeryVerbose()->willReturn(true)->shouldBeCalled();
        $io->writeError(Argument::type('string'))->will(
            function ($args) use (&$output) {
                $output[] = $args[0];
            }
        )->shouldBeCalled();
        $io->write(Argument::type('string'))->shouldNotBeCalled();

        $fixture = new Logger('test', $io->reveal());
        $fixture->debug('foo');
        $this->assertEquals(1, count($output));
        $this->assertStringContainsString('<info>[test]</info>', $output[0]);
    }

    public function testNotVeryVerboseDebug()
    {
        $io = $this->prophesize(\Composer\IO\IOInterface::class);
        $io->isVeryVerbose()->willReturn(false)->shouldBeCalled();
        $io->writeError(Argument::type('string'))->shouldNotBeCalled();
        $io->write(Argument::type('string'))->shouldNotBeCalled();

        $fixture = new Logger('test', $io->reveal());
        $fixture->debug('foo');
    }

    public function testVerboseInfo()
    {
        $output = [];
        $io = $this->prophesize(\Composer\IO\IOInterface::class);
        $io->isVerbose()->willReturn(true)->shouldBeCalled();
        $io->writeError(Argument::type('string'))->will(
            function ($args) use (&$output) {
                $output[] = $args[0];
            }
        )->shouldBeCalled();
        $io->write(Argument::type('string'))->shouldNotBeCalled();

        $fixture = new Logger('test', $io->reveal());
        $fixture->info('foo');
        $this->assertCount(1, $output);
        $this->assertStringContainsString('<info>[test]</info>', $output[0]);
    }

    public function testNotVerboseInfo()
    {
        $io = $this->prophesize(\Composer\IO\IOInterface::class);
        $io->isVerbose()->willReturn(false)->shouldBeCalled();
        $io->writeError(Argument::type('string'))->shouldNotBeCalled();
        $io->write(Argument::type('string'))->shouldNotBeCalled();

        $fixture = new Logger('test', $io->reveal());
        $fixture->info('foo');
    }

    public function testWarning()
    {
        $output = [];
        $io = $this->prophesize(\Composer\IO\IOInterface::class);
        $io->writeError(Argument::type('string'))->will(
            function ($args) use (&$output) {
                $output[] = $args[0];
            }
        )->shouldBeCalled();
        $io->write(Argument::type('string'))->shouldNotBeCalled();

        $fixture = new Logger('test', $io->reveal());
        $fixture->warning('foo');
        $this->assertCount(1, $output);
        $this->assertStringContainsString('<error>[test]</error>', $output[0]);
    }
}
// vim:sw=4:ts=4:sts=4:et:
