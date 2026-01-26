<?php
namespace toubilib\api\middlewares;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;
use toubilib\core\application\ports\api\AuthzPraticienServiceInterface;

class AuthzPraticienMiddleware {
    private AuthzPraticienServiceInterface $authzPraticien;
    
    public function __construct(AuthzPraticienServiceInterface $authzPraticien) {
        $this->authzPraticien = $authzPraticien;
    }
    
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        try {
            $authDto = $request->getAttribute('authenticated_user');
            if (!$authDto) {
                throw new Exception("Erreur authentification: Authentification requise");
            }
            
            $routeContext = RouteContext::fromRequest($request);
            $id = $routeContext->getRoute()->getArgument('id');
            
            $operation = $this->getOperationFromMethod($request->getMethod());
            
            $this->authzPraticien->isGranted($authDto->id, $authDto->role, $id, $operation);
            
            return $handler->handle($request);
            
        } catch (Exception $e) {
            
            $response = new Response();
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'message' => $e->getMessage()
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json');
        }
    }
    
    private function getOperationFromMethod(string $method): int {
        switch ($method) {
            case 'GET':
                return $this->authzPraticien->OPERATION_READ;
            case 'POST':
                return $this->authzPraticien->OPERATION_CREATE;
            case 'PUT':
            case 'PATCH':
                return $this->authzPraticien->OPERATION_UPDATE;
            case 'DELETE':
                return $this->authzPraticien->OPERATION_DELETE;
            default:
                return $this->authzPraticien->OPERATION_READ;
        }
    }
}