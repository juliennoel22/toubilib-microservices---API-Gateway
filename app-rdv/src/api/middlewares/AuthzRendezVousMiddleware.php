<?php
namespace toubilib\api\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;
use toubilib\core\application\ports\api\AuthzRDVServiceInterface;

class AuthzRendezVousMiddleware {
    private AuthzRDVServiceInterface $authzRdv;
    
    public function __construct(AuthzRDVServiceInterface $authzRdv) {
        $this->authzRdv = $authzRdv;
    }
    
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $authDto = $request->getAttribute('authenticated_user');
        
        if (!$authDto) {
            $response = new Response();
            $response->getBody()->write(json_encode(['type' => 'error', 'error' => 401, 'message' => 'Authentication required']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
        
        try {
            $routeContext = RouteContext::fromRequest($request);
            $id = $routeContext->getRoute()->getArgument('id');
            $operation = $this->getOperation($request);
            
            $this->authzRdv->isGranted($authDto->id, $authDto->role, $id, $operation);
            
            return $handler->handle($request);
            
        } catch (\Exception $e) {
            $status = strpos($e->getMessage(), 'autorisation') !== false ? 403 : 401;
            $response = new Response();
            $response->getBody()->write(json_encode(['type' => 'error', 'error' => $status, 'message' => $e->getMessage()]));
            return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
        }
    }
    
    private function getOperation(ServerRequestInterface $request): int {
        $method = $request->getMethod();
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute()->getPattern();
        
        if ($method === 'PATCH' && strpos($route, '/annuler') !== false) {
            return $this->authzRdv->OPERATION_DELETE;
        }
        
        return match($method) {
            'GET' => $this->authzRdv->OPERATION_READ,
            'POST' => $this->authzRdv->OPERATION_CREATE,
            'PUT', 'PATCH' => $this->authzRdv->OPERATION_UPDATE,
            'DELETE' => $this->authzRdv->OPERATION_DELETE,
            default => $this->authzRdv->OPERATION_READ
        };
    }
}