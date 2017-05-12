<?php
$handler = function ($request)
{
	$request = Request::fromString($request);
	switch ($request->uri)
	{
		case '/index' :
			$response = new Response($request->uri);
			$response->setStatusCode(200);
			break;
		case '/test' :
			$response = new Response($request->uri);
			$response->setStatusCode(200);
			break;
		default :
			$response = new Response($request->uri);
			$response->setStatusCode(404);

	}

	return $response;
};

return $handler;