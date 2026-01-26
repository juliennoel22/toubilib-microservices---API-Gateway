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

        // Transfert des headers
        $headers = $request->getHeaders();
        unset($headers['Host']);
        unset($headers['Content-Length']);

        $options = [
            'headers' => $headers,
            'http_errors' => false,
        ];

        // Transfert du corps de la requête
        $contents = (string) $request->getBody();
        if (!empty($contents)) {
            $options['body'] = $contents;
        }

        try {
            $apiResponse = $this->client->request($method, $uri, $options);

            return $apiResponse;

        } catch (ConnectException $e) {
            throw new HttpInternalServerErrorException($request, "Service unavailable !", $e);
        } catch (ServerException $e) {
            throw new HttpInternalServerErrorException($request, "Upstream server error", $e);
        }
    }
}
