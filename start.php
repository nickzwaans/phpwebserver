<?php

include './Server.php';
include './Request.php';
include './Response.php';

$server = new Server('0.0.0.0', 8000);
$server->listen(function ($request)
{
	// Dit is zodat de server kan blijven draaien terwijl je handleRequest file verandert
	$handler = include('./handleRequest.php');
	return $handler($request);
});
