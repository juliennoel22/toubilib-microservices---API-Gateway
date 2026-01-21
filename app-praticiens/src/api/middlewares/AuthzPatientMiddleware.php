<?php
namespace toubilib\api\middlewares;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;
use toubilib\core\application\ports\api\AuthzPatientServiceInterface;

class AuthzPatientMiddleware {
    private AuthzPatientServiceInterface $authzPatient;
    
    public function __construct(AuthzPatientServiceInterface $authzPatient) {
        $this->authzPatient = $authzPatient;
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
            
            $this->authzPatient->isGranted($authDto->id, $authDto->role, $id, $operation);
            
            return $handler->handle($request);
            
        } catch (Exception $e) {
            $status = (strpos($e->getMessage(), "Erreur autorisation") === 0) ? 403 : 401;
            
            $response = new Response();
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => $status,
                'message' => $e->getMessage()
            ]));
            
            return $response
                ->withStatus($status)
                ->withHeader('Content-Type', 'application/json');
        }
    }
    
    private function getOperationFromMethod(string $method): int {
        switch ($method) {
            case 'GET':
                return $this->authzPatient->OPERATION_READ;
            case 'POST':
                return $this->authzPatient->OPERATION_CREATE;
            case 'PUT':
            case 'PATCH':
                return $this->authzPatient->OPERATION_UPDATE;
            case 'DELETE':
                return $this->authzPatient->OPERATION_DELETE;
            default:
                return $this->authzPatient->OPERATION_READ;
        }
    }
}