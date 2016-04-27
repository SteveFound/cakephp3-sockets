# CakePHP3 Socket Library

The idea of this library is a set of Socket Decorators that add to the functionality of the Http Client library of CakePHP3. The library is work
in progress and is updated from the same library I created for CakePHP2.

## Installation

You should install this package with composer:

	composer require dns/cakephp-sockets:dev-master

## Usage

To use the classes in this package, you simply need to include them in your PHP files using the 'use' keyword to access the namespace of the class. Composer will automatically add the files to your autoloader when you require the package or do a composer update.

The classes supplied are:

__Dns\Socket\ThrottledRequestSocket__

__Dns\Socket\CachedResponseSocket__

__Dns\Socket\CakeHttpClientSocket__

These classes all implement the interface __Dns\Socket\Contracts\SocketInterface__

## SocketInterface

The interface Dns\Socket\Contracts\SocketInterface simply defines the methods that
all sockets must implement.

## CakeHttpClientSocket

The class Dns\Socket\CakeHttpClientSocket wraps CakePHP's own Cake\Network\Http\Client class
to make it conform to the interface so that it may be used with the other socket classes. The
usage of the get(), put(), post() and delete() methods are as detailed in the [CakePHP documentation](http://book.cakephp.org/3.0/en/core-libraries/httpclient.html). The difference is that if successful, the methods return the body of the response
and if the request fails, they return false. You do not get a Response object returned.

```php

use Cake\Network\Http\Client;
use Dns\Socket\CakeHttpClientSocket;

/* Create a CakePHP Client */
$client = new Client();
/* Create a CakeHttpClientSocket() to wrap the client */
$socket = new CakeHttpClientSocket($client);

/* The functions get(), put(), post() and delete() will now return the body of a response if
 * the request is successful or false if it errors.
 */
$body = $socket->get('http://example.com/search', ['q' => 'widget'], [
  'headers' => ['X-Requested-With' => 'XMLHttpRequest']
]);
if ($body) {
	// Process response body
}
```

## CachedResponseSocket

The class Dns\Socket\CachedResponseSocket wraps another socket implementation and caches the
response of that socket if the request is successful. The constructor to CachedResponseSocket takes
the socket it is wrapping and the cache configuration you want to use to control how long the
response is cached for and the location. The configuration is supplied as a string which is the
configuration name as defined in the app.php configuration file or one you have defined locally.
CacheResponseSocket uses the standard Cake\Cache\Cache class. If no configuration name is supplied
'default' is assumed.

```php

use Cake\Cache\Cache;
use Cake\Network\Http\Client;
use Dns\Socket\CakeHttpClientSocket;
use Dns\Socket\CachedResponseSocket;


/* Cache configuration */
Cache::config('__socket__', [
    'className' => 'File',
    'duration' => '+1 hours',
    'path' => CACHE,
    'prefix' => '__socket__'
]);

/* Create a CakeHttpClientSocket() to wrap the client */
$http = new CakeHttpClientSocket(new Client());

/* Create a cached socket */
$cache = new CachedResponseSocket($http, '__socket__');

/*
 * The functions get(), put(), post() and delete() will now return the body of a response if
 * the request is successful or false if it errors.
 */
$body = $cache->get('http://example.com/search', ['q' => 'widget']);
if ($body) {
	// Process response body
}
```
The code above will make a get request to 'http://example.com/search?q=widget'. If a valid response is
returned, the body of the response will cached in the CACHE directory for one hour. The cache file will be
called '\_\_socket\_\_' . md5('gethttp://example.com/search?q=widget'). Note that 'get' is prepended to the URL
so that get, post, put and delete requests will all be cached in different files.

If another get request is issued to the same URL within the next hour, the cached response will be returned
and the request to the server will never be made.

## ThrottledRequestSocket

This socket will only allow one request per second to be made from the server, thereby throttling requests.
When making requests to services such as Amazon Product Advertising, you will get blocked after a certain
time if you continually make more than one request per second. This socket will ensure that does not happen.
The socket works be keeping track of when the last request was made by writing the time to a file that you
supply in the constructor. If the file does not exist, the constructor will create it and write the current
time. If the file does exist, the constructor will leave it as another client must have created it.

When a get(), post(), put() or delete() request is made, the time in the file is checked against the current
time. If the current time is less than the time in the file then more requests from other server clients have
been made. One second is added to the time and this is written back to the file. The client then sleeps until
it is the time it wrote to the file and makes the request. If the time in the file is less than or equal to the
current time, then the current time is written to the file and the request is made immediately.

```php
use Cake\Cache\Cache;
use Cake\Network\Http\Client;
use Dns\Socket\CakeHttpClientSocket;
use Dns\Socket\ThrottledRequestSocket;


/* Create a CakeHttpClientSocket() to wrap the client */
$http = new CakeHttpClientSocket(new Client());

/* Create a throttled socket */
$throttled = new ThrottledRequestSocket($http, CACHE . '/throttle.dat');

/*
 * The functions get(), put(), post() and delete() will now return the body of a response if
 * the request is successful or false if it errors.
 */
$body = $throttled->get('http://example.com/search', ['q' => 'widget']);
if ($body) {
	// Process response body
}
```

The code above will make a get request to 'http://example.com/search?q=widget' with the guarantee that
not more than one request per second will originate from your server.

### Amazon Product Advertising

The Amazon Product Advertising API was the API that prompted me to create this library. The usage of both
the Caching and Throttling sockets make it an ideal fit since if more than on user wants to view the same
Amazon item on your server, they will be accessing a cached item of it and if there is no cached version,
it will not hit the Amazon service with more than one request per second.

To achieve this, we need to create an CakeHttpClientSocket, throttle it with ThrottledRequestSocket and
Cache the throttled socket with the CachedResponseSocket. It's vital that we do this correctly or we will
be throttling a cached socket which will cache the response but limit access to the cache to once a second.
Not what we desire in this instance.

so we do...

```php
use Cake\Cache\Cache;
use Cake\Network\Http\Client;
use Dns\Socket\CakeHttpClientSocket;
use Dns\Socket\ThrottledRequestSocket;
use Dns\Socket\CachedResponseSocket;


/* Cache configuration */
Cache::config('__socket__', [
    'className' => 'File',
    'duration' => '+1 hours',
    'path' => CACHE,
    'prefix' => '__socket__'
]);


/* Create a CakeHttpClientSocket() to wrap the client */
$http = new CakeHttpClientSocket(new Client());

/* Create a throttled socket */
$throttled = new ThrottledRequestSocket($http, CACHE . '/throttle.dat');

/* Create a cached socket */
$cache = new CachedResponseSocket($throttled, '__socket__');

/*
 * The functions get(), put(), post() and delete() will now return the body of a response if
 * the request is successful or false if it errors.
 */
$body = $cache->get('http://amazon.co.uk?lots&of&parameters');
if ($body) {
	// Process response body
}
```
