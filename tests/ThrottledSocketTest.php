<?php

use Dns\Socket\Contracts\SocketInterface;
use Dns\Socket\ThrottledRequestSocket;
use \Mockery as m;

/**
 * All Throttled socket actually does is delay the request for a given time using the getDelay() function to calculate for how long
 * Here we are basically testing the getDelay() function.
 */
class ThrottledSocketTest extends \PHPUnit_Framework_TestCase
{
    public $file;

    /**
     * Delete the throttle file for each test
     * @return void
     */
    public function setUp()
    {
        $this->file = __DIR__ . '/cache/throttle.dat';
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    /**
     * Shut down mockery
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test that for one call, the throttle file is created and there is no delay
     * @return void
     */
    public function testFirstCall()
    {
        $dummySocket = m::mock('Dns\Socket\Contracts\SocketInterface');
        $sock = new MyThrottledSocket($dummySocket, $this->file);
        $this->assertFileExists($this->file);
        $nbr = intval(trim(file_get_contents($this->file)));
        $this->assertEquals($nbr, 0);
        $msg = $sock->get(null);
        $this->assertEquals($msg, 'Hello World');
        $this->assertEquals($sock->delay, 0);
    }

    /**
     * Test that if the throttle timestamp is in the future, it is incremented and the correct number
     * of seconds to wait is returned
     * @return void
     */
    public function testThrottledCall()
    {
        $dummySocket = m::mock('Dns\Socket\Contracts\SocketInterface');
        $sock = new MyThrottledSocket($dummySocket, $this->file);
        $this->assertFileExists($this->file);

        $t = time();
        file_put_contents($this->file, $t + 3);
        $msg = $sock->get(null);
        $nbr = intval(trim(file_get_contents($this->file)));

        $this->assertEquals($t + 4, $nbr);
        $this->assertEquals($msg, 'Hello World');
        $this->assertEquals(4, $sock->delay);
    }

    /**
     * Test that if the throttle timestamp is in the past, it is set to now and
     * no wait period is returned.
     * @return void
     */
    public function testUnthrottledCall()
    {
        $dummySocket = m::mock('Dns\Socket\Contracts\SocketInterface');
        $sock = new MyThrottledSocket($dummySocket, $this->file);
        $this->assertFileExists($this->file);

        $t = time();
        file_put_contents($this->file, $t - 3);
        $msg = $sock->get(null);
        $nbr = intval(trim(file_get_contents($this->file)));

        $this->assertEquals($nbr, $t);
        $this->assertEquals($msg, 'Hello World');
        $this->assertEquals($sock->delay, 0);
    }
}

// @codingStandardsIgnoreStart

/**
 * In ThrottledSocket, the getDelay function is protected as it should never be called directly in normal usage.
 * Here we want to test it so this class provides a get() function which makes the returned delay visible for testing.
 */
class MyThrottledSocket extends ThrottledRequestSocket implements SocketInterface
{
    public $delay;

    public function get($uri, array $data = [], array $options = [])
    {
        $this->delay = $this->getDelay();
        return "Hello World";
    }
}
// @codingStandardsIgnoreEnd
