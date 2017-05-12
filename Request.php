<?php

/**
 * Class Request
 */
class Request {

	/**
	 * @var string
	 */
	/**
	 * @var string
	 */
	/**
	 * @var array|string
	 */
	/**
	 * @var array|string
	 */
	private $method, $uri, $headers, $validTest;

	/**
	 * @var array
	 */
	private $validMagicKeys = [];

	/**
	 * Request constructor.
	 *
	 * @param string $method
	 * @param string $uri
	 * @param array $headers
	 */
	public function __construct(string $method, string $uri = null, array $headers = null)
	{
		$this->method = $method;
		$this->uri = strtolower($uri);
		if (isset($headers))
		{
			$this->headers = $headers;
		}
	}

	/**
	 * @param $key
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function __get($key)
	{
		return $this->get($key);
	}

	/**
	 * @param $key
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function get($key){
		$this->createValidMagicKeys();
		if (in_array($key, $this->validMagicKeys))
		{
			if (isset($this->$key))
			{
				return $this->$key;
			}
			throw new Exception('Cannot find key');
		}
	}

	/**
	 * @param string $string
	 *
	 * @return \Request
	 */
	public static function fromString(string $string) : Request
	{
		$lines = explode("\n", $string);

		list($method, $uri) = explode(' ', array_shift($lines));

		$headers = [];
		foreach ($lines as $line)
		{
			$line = trim($line);
			if (strpos($line, ': ') !== false)
			{
				list($key, $value) = explode(': ', $line);
				$headers[ $key ] = $value;
			}
		}

		return new self($method, $uri, $headers);
	}

	/**
	 * @return array
	 */
	private function createValidMagicKeys(){
		return $this->validMagicKeys = ['method', 'uri', 'headers'];
	}
}