<?php

class Server
{
	/**
	 * The current host
	 *
	 * @var string
	 */
	protected $host = null;

	/**
	 * The current port
	 *
	 * @var int
	 */
	protected $port = null;

	/**
	 * The binded socket
	 *
	 * @var resource
	 */
	protected $socket = null;

	/**
	 * Construct new Server instance
	 *
	 * @param string $host
	 * @param int $port
	 *
	 * @return void
	 */
	public function __construct($host, $port)
	{
		$this->host = $host;
		$this->port = (int) $port;

		// create a socket
		$this->createSocket();

		// bind the socket
		$this->bind();
	}

	/**
	 *  Create new socket resource
	 *
	 * @return void
	 */
	protected function createSocket()
	{
		$this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
	}

	/**
	 * Bind the socket resourece
	 *
	 * @return void
	 */
	protected function bind()
	{
		if ( ! socket_bind($this->socket, $this->host, $this->port))
		{
			throw new Exception('Could not bind: ' . $this->host . ':' . $this->port . ' - ' . socket_strerror(socket_last_error()));
		}

		printf('Started listening on %s:%s.%s', $this->host, $this->port, "\n");
	}

	/**
	 * Listen for requests
	 *
	 * @param callable $callback
	 *
	 * @return void
	 */
	public function listen($callback)
	{
		// check if the callback is valid
		if ( ! is_callable($callback))
		{
			throw new Exception('The given argument should be callable.');
		}

		while (1)
		{
			// listen for connections
			socket_listen($this->socket);

			// try to get the client socket resource
			// if false we got an error close the connection and continue
			if ( ! $client = socket_accept($this->socket))
			{
				socket_close($client);
				continue;
			}

			// create new request instance with the clients header.
			// In the real world of course you cannot just fix the max size to 1024..
			$request = socket_read($client, 2048);

			// execute the callback 
			$response = call_user_func($callback, $request);

			// write the response to the client socket
			socket_write($client, $response, strlen($response));

			// close the connection so we can accept new ones
			socket_close($client);
		}
	}
}