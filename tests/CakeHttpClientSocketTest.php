<?php

use Cake\Network\Http\Client;
use Cake\Network\Http\Response;
use Dns\Socket\CakeHttpClientSocket;
use \Mockery as m;

/**
 * All Throttled socket actually does is delay the request for a given time using the getDelay() function to calculate for how long
 * Here we are basically testing the getDelay() function.
 */
class CakeHttpClientSocketTest extends \PHPUnit_Framework_TestCase
{
    public $file;

    /**
     * Shutdown Mockery
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test that a call to get() returns the body of a response
     * @return void
     */
    public function testGet()
    {
        $response = new Response(['HTTP/1.1 200 OK'], 'Response Body !');
        $client = m::mock('Cake\Network\Http\Client');
        $client->shouldReceive('get')->with('http://example.com', [], [])->andReturn($response);

        $socket = new CakeHttpClientSocket($client);
        $str = $socket->get('http://example.com');

        $this->assertEquals('Response Body !', $str);
    }

    /**
     * Test that a call to post() returns the body of a response
     * @return void
     */
    public function testPost()
    {
        $response = new Response(['HTTP/1.1 200 OK'], 'Response Body !');
        $client = m::mock('Cake\Network\Http\Client');
        $client->shouldReceive('post')->with('http://example.com', [], [])->andReturn($response);

        $socket = new CakeHttpClientSocket($client);
        $str = $socket->post('http://example.com');

        $this->assertEquals('Response Body !', $str);
    }

    /**
     * Test that a call to delete() returns the body of a response
     * @return void
     */
    public function testDelete()
    {
        $response = new Response(['HTTP/1.1 200 OK'], 'Response Body !');
        $client = m::mock('Cake\Network\Http\Client');
        $client->shouldReceive('delete')->with('http://example.com', [], [])->andReturn($response);

        $socket = new CakeHttpClientSocket($client);
        $str = $socket->delete('http://example.com');

        $this->assertEquals('Response Body !', $str);
    }

    /**
     * Test that a call to put() returns the body of a response
     * @return void
     */
    public function testPut()
    {
        $response = new Response(['HTTP/1.1 200 OK'], 'Response Body !');
        $client = m::mock('Cake\Network\Http\Client');
        $client->shouldReceive('put')->with('http://example.com', [], [])->andReturn($response);

        $socket = new CakeHttpClientSocket($client);
        $str = $socket->put('http://example.com');

        $this->assertEquals('Response Body !', $str);
    }

    /**
     * Test that when a request fails, a call to get() returns false
     * @return void
     */
    public function testFailedGet()
    {
        $response = new Response(['HTTP/1.1 404 Not Found'], null);
        $client = m::mock('Cake\Network\Http\Client');
        $client->shouldReceive('get')->with('http://example.com', [], [])->andReturn($response);

        $socket = new CakeHttpClientSocket($client);
        $str = $socket->get('http://example.com');

        $this->assertFalse($str);
    }

    /**
     * Test that when a request fails, a call to post() returns false
     * @return void
     */
    public function testFailedPost()
    {
        $response = new Response(['HTTP/1.1 404 Not Found'], null);
        $client = m::mock('Cake\Network\Http\Client');
        $client->shouldReceive('post')->with('http://example.com', [], [])->andReturn($response);

        $socket = new CakeHttpClientSocket($client);
        $str = $socket->post('http://example.com');

        $this->assertFalse($str);
    }

    /**
     * Test that when a request fails, a call to delete() returns false
     * @return void
     */
    public function testFailedDelete()
    {
        $response = new Response(['HTTP/1.1 404 Not Found'], null);
        $client = m::mock('Cake\Network\Http\Client');
        $client->shouldReceive('delete')->with('http://example.com', [], [])->andReturn($response);

        $socket = new CakeHttpClientSocket($client);
        $str = $socket->delete('http://example.com');

        $this->assertFalse($str);
    }

    /**
     * Test that when a request fails, a call to put() returns false
     * @return void
     */
    public function testFailedPut()
    {
        $response = new Response(['HTTP/1.1 404 Not Found'], null);

        $client = m::mock('Cake\Network\Http\Client');
        $client->shouldReceive('put')->with('http://example.com', [], [])->andReturn($response);

        $socket = new CakeHttpClientSocket($client);
        $str = $socket->put('http://example.com');

        $this->assertFalse($str);
    }
}
