<?php

class Response {
	//response class weet niets van views, views moeten hieruit en mee worden gegeven vanuit main.

	private $body;

	private $statusCode = 200;

	private $headers = [];

	public static $statusCodes = [
		// Informational 1xx
		100 => 'Continue',
		101 => 'Switching Protocols',

		// Success 2xx
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',

		// Redirection 3xx
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		// 1.1
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		// 306 is deprecated but reserved
		307 => 'Temporary Redirect',

		// Client Error 4xx
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',

		// Server Error 5xx
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		509 => 'Bandwidth Limit Exceeded'
	];

	public function __construct(string $body = '')
	{
		$this->body = $this->getBody($body);
		//set headers;
		$this->header('Date', gmdate('D, d M Y H:i:s T'));
		$this->header('Content-Type', 'text/html');
		$this->header('Server', 'php swanu');
		$this->header('content-length', strlen($this->body));
	}

	public function toString()
	{
		return $this->formatHeadersAsString() . $this->body;
	}

	public function __toString()
	{
		return $this->toString();
	}

	public function setStatusCode($statusCode)
	{
		return $this->statusCode = $statusCode;
	}

	public function header($key, $value)
	{
		$key = array_map('ucwords', explode('-', strtolower($key)));
		$key = implode('-', $key);
		$this->headers[ $key ] = $value;
	}

	private function getBody(string $body)
	{
		return $this->getFilePath($body);
	}

	private function getFilePath(string $uri)
	{
		$path = str_replace('/', 'views/', $uri) . '.php';
		if (file_exists($path))
		{
			return $this->parseContentBodyFromFile($path);
		}

		return 'page not found';
	}

	private function parseContentBodyFromFile(string $file)
	{
		if (file_exists($file))
		{
			ob_start();
			include $file;
			$contentBody = ob_get_clean();

			return $contentBody;
		}

		return $file;
	}

	private function formatHeadersAsString()
	{
		$lines = [];
		$lines[] = "HTTP/1.1 " . $this->statusCode . ' ' . static::$statusCodes[ $this->statusCode ];
		foreach ($this->headers as $key => $value)
		{
			$lines[] = $key . ": " . $value;
		}

		return implode(" \r\n", $lines) . "\r\n\r\n";
	}
}