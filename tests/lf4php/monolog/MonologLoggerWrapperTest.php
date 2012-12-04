<?php
/*
 * Copyright (c) 2012 Szurovecz János
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace lf4php\monolog;

use PHPUnit_Framework_TestCase;

/**
 * @author Szurovecz János <szjani@szjani.hu>
 */
class MonologLoggerWrapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MonologLoggerFactory
     */
    private $monologFactory;

    /**
     * @var \Monolog\Logger
     */
    private $monolog;

    public function setUp()
    {
        $this->monologFactory = new MonologLoggerFactory();
        $this->monolog = new \Monolog\Logger('foo');
        $this->monologFactory->registerLogger($this->monolog);
    }

    public function testRegisterLogger()
    {
        $found = $this->monologFactory->getLogger('foo');
        self::assertSame($this->monolog, $found->getMonologLogger());
    }

    public function testDefaultLogger()
    {
        $found = $this->monologFactory->getLogger('notExists');
        self::assertEquals(\lf4php\Logger::ROOT_LOGGER_NAME, $found->getName());
        self::assertEquals($found->getName(), $found->getMonologLogger()->getName());
    }

    public function testCheckAncestorFind()
    {
        $found = $this->monologFactory->getLogger('\foo\bar');
        self::assertSame($this->monolog, $found->getMonologLogger());
    }

    public function testTrace()
    {
        $logfile = __DIR__ . DIRECTORY_SEPARATOR . 'testTrace.log';
        $streamHandler = new \Monolog\Handler\StreamHandler($logfile);
        $this->monolog->pushHandler($streamHandler);
        $found = $this->monologFactory->getLogger('foo');
        $found->trace('Hello {{name}}! Ouch!', array('name' => 'John'));

        $content = file_get_contents($logfile);
        self::assertRegExp('/Hello John!/', $content);
        $streamHandler->close();
        unlink($logfile);
    }
}