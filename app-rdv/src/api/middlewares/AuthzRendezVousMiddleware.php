<?php
namespace toubilib\api\middlewares;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;
use toubilib\core\application\ports\api\AuthzRDVServiceInterface;

class AuthzRendezVousMiddleware
{
    private AuthzRDVServiceInterface $authzRdv;
    public function __construct(AuthzRDVServiceInterface $authzRdv)
    {
        $this->authzRdv = $authzRdv;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            // TD 2.2 Exercice 5: Extract and decode JWT to get user role/id
            // Validation is already done by Gateway, so we just decode.

            if (!$request->hasHeader('Authorization')) {
                throw new Exception("Erreur authentification: Token manquant");
            }

            $authHeader = $request->getHeaderLine('Authorization');
            $token = sscanf($authHeader, "Bearer %s")[0] ?? null;

            if (!$token) {
                throw new Exception("Erreur authentification: Format de token invalide");
            }

            // Décodage du token sans validation (déjà fait par la Gateway)
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                throw new Exception("Erreur authentification: Token malformé");
            }

            $payload = json_decode(base64_decode($parts[1]), true);

            $userId = $payload['sub'] ?? null;
            $userRole = $payload['upr']['role'] ?? 0;

            if (!$userId) {
                throw new Exception("Erreur authentification: Token incomplet (sub manquant)");
            }

            $routeContext = RouteContext::fromRequest($request);
            $id = $routeContext->getRoute()->getArgument('id');

            $operation = $this->getOperationFromMethod($request->getMethod());

            if ($request->getMethod() === 'PATCH') {
                $route = $routeContext->getRoute()->getPattern();
                if (strpos($route, '/annuler') !== false) {
                    $operation = AuthzRDVServiceInterface::OPERATION_DELETE;
                }
            }

            $this->authzRdv->isGranted($userId, $userRole, $id, $operation);

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

    private function getOperationFromMethod(string $method): int
    {
        switch ($method) {
            case 'GET':
                return AuthzRDVServiceInterface::OPERATION_READ;
            case 'POST':
                return AuthzRDVServiceInterface::OPERATION_CREATE;
            case 'PUT':
            case 'PATCH':
                return AuthzRDVServiceInterface::OPERATION_UPDATE;
            case 'DELETE':
                return AuthzRDVServiceInterface::OPERATION_DELETE;
            default:
                return AuthzRDVServiceInterface::OPERATION_READ;
        }
    }
}