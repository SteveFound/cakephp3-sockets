<?php
use Cake\Cache\Cache;
use Dns\Socket\CachedResponseSocket;
use Dns\Socket\Contracts\SocketInterface;
use Dns\Socket\RepositoryFactory;
use \Mockery as m;

class CachedSocketTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Shut down Mockery
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test the caching
     * @return void
     */
    public function testCaching()
    {
        $firstSocket = m::mock('Dns\Socket\Contracts\SocketInterface');
        $firstSocket->shouldReceive('get')->andReturn('GET first socket');

        $secondSocket = m::mock('Dns\Socket\Contracts\SocketInterface');
        $secondSocket->shouldReceive('get')->andReturn('GET second socket');

        $config = [
            'className' => 'File',
            'duration' => '+1 hours',
            'path' => __DIR__ . '/cache',
            'prefix' => '__socket__'
        ];
        Cache::config('__socket__', $config);

        $url = 'http://example.com/somedata';
        $key = md5($url);

        $sock = new CachedResponseSocket($firstSocket, '__socket__');
        $sock->setKey($key);
        $response = $sock->get($url);
        $str = Cache::read($key, '__socket__');
        $this->assertNotNull($str);
        $this->assertEquals($response, "GET first socket");
        $this->assertEquals($str, "GET first socket");

        $sock = new CachedResponseSocket($secondSocket, '__socket__');
        $sock->setKey($key);
        $response = $sock->get($url);
        $str = Cache::read($key, '__socket__');
        $this->assertNotNull($str);
        $this->assertEquals($response, "GET first socket");
        $this->assertEquals($str, "GET first socket");

        $sock->forget();
        $str = Cache::read($key, '__socket__');
        $this->assertFalse($str);
    }

    /**
     * Test forgetting the cached response
     * @return void
     */
    public function testForget()
    {
        $firstSocket = m::mock('Dns\Socket\Contracts\SocketInterface');
        $firstSocket->shouldReceive('get')->andReturn('GET first socket');

        $secondSocket = m::mock('Dns\Socket\Contracts\SocketInterface');
        $secondSocket->shouldReceive('get')->andReturn('GET second socket');

        $url = 'http://example.com/somedata';
        $key = md5($url);

        $sock = new CachedResponseSocket($firstSocket, '__socket__');
        $sock->setKey($key);
        $response = $sock->get($url);
        $str = Cache::read($key, '__socket__');
        $this->assertNotNull($str);
        $this->assertEquals($response, "GET first socket");
        $this->assertEquals($str, "GET first socket");

        $sock->forget();
        $str = Cache::read($key, '__socket__');
        $this->assertFalse($str);

        $sock = new CachedResponseSocket($secondSocket, '__socket__');
        $sock->setKey($key);
        $response = $sock->get($url);
        $str = Cache::read($key, '__socket__');
        $this->assertNotNull($str);
        $this->assertEquals($response, "GET second socket");
        $this->assertEquals($str, "GET second socket");

        $sock->forget();
        $str = Cache::read($key, '__socket__');
        $this->assertFalse($str);
    }
}
