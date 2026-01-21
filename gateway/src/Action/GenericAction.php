<?php

namespace toubilib\gateway\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpInternalServerErrorException;

class GenericAction
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        $query = $request->getUri()->getQuery();

        $uri = $path;
        if (!empty($query)) {
            $uri .= '?' . $query;
        }

        try {
            $options = [];
            if ($request->getBody()->getSize() > 0) {
                 $options['body'] = $request->getBody()->getContents(); 
            }

            // Guzzle Request
            $apiResponse = $this->client->request($method, $uri, $options);

            // Guzzle response implements PSR-7 ResponseInterface, so it is compatible with Slim
            return $apiResponse;

        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 404) {
                throw new HttpNotFoundException($request, "Ressource inexistante");
            }
            throw $e;
        } catch (ConnectException $e) {
            throw new HttpInternalServerErrorException($request, "Service unavailable !", $e);
        } catch (ServerException $e) {
             throw new HttpInternalServerErrorException($request, "Upstream server error", $e);
        }
    }
}
