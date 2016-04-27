<?php
namespace Dns\Socket;

use Dns\Socket\Contracts\SocketInterface;

/**
 * A throttled request socket ensures that only one request per second is issued from the server. A system wide file is used to
 * store the time of the last request. When a request is made, the current time is compared with the time in the file. If that time
 * is in the future, a second is added to it and it is written back. The new time is the time that the request is allowed to be made so the
 * process sleeps until that time then wakes up and makes the request. If the time in the file is less than the current time then the time
 * is updated and the request can be made immediately.
 *
 * @author Steve Found (DnSMedia)
 */
class ThrottledRequestSocket extends AbstractSocket implements SocketInterface
{
    /**
     * @var FileSystem
     */
    protected $files;

    /**
     * @var string
     */
    protected $filename;

    /**
     * Create the object and decorate another socket
     * @param SocketInterface $socket   The socket being throttled
     * @param string $filename          Where to store throttling timestamp
     */
    public function __construct(SocketInterface $socket, $filename = '/tmp/throttle.dat')
    {
        parent::__construct($socket);
        $this->filename = $filename;
        if (!file_exists($filename)) {
            file_put_contents($filename, '0');
        }
    }

    /**
     *
     * Issues a DELETE request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request
     * @param array $data    Array of PUT data keys and values.
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function delete($uri, array $data, array $options)
    {
        $this->throttle();
        return parent::delete($uri, $data, $options);
    }

    /**
     * Issues a POST request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request
     * @param array $data    Array of PUT data keys and values.
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function post($uri, array $data, array $options)
    {
        $this->throttle();
        return parent::post($uri, $data, $options);
    }

    /**
     * Issues a GET request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request
     * @param array $data    Array of PUT data keys and values.
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function get($uri, array $data, array $options)
    {
        $this->throttle();
        return parent::get($uri, $data, $options);
    }

    /**
     * Issues a PUT request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request
     * @param array $data    Array of PUT data keys and values.
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function put($uri, array $data, array $options)
    {
        $this->throttle();
        return parent::put($uri, $data, $options);
    }

    /**
     * Introduce a delay
     * @return void
     */
    protected function throttle()
    {
        $delay = $this->getDelay();
        if ($delay > 0) {
            sleep($delay);
        }
    }

    /**
     * Calculate the number of seconds until the next request can be made
     * @return Number of seconds to wait before request can be made.
     */
    protected function getDelay()
    {
        $curtime = time();
        $nbr = file_get_contents($this->filename);
        $filetime = intval(trim($nbr));
        if ($curtime > $filetime) {
            $filetime = $curtime;
        } else {
            $filetime++;
        }
        file_put_contents($this->filename, $filetime);
        return $filetime - $curtime;
    }
}
