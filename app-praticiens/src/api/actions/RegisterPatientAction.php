<?php
namespace toubilib\api\actions;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use toubilib\core\application\ports\api\ServicePatientInterface;

class RegisterPatientAction {
    private ServicePatientInterface $servicePatient;

    public function __construct(ServicePatientInterface $servicePatient) {
        $this->servicePatient = $servicePatient;
    }

    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response
    ): ResponseInterface {
        
        $data = $request->getParsedBody();
        
        $required = ['nom', 'prenom', 'date_naissance', 'adresse', 'code_postal', 'ville', 'email', 'telephone', 'password'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $error = [
                    'type' => 'error',
                    'error' => 400,
                    'message' => "Field {$field} is required"
                ];
                $response->getBody()->write(json_encode($error));
                return $response
                    ->withStatus(400)
                    ->withHeader('Content-Type', 'application/json');
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $error = [
                'type' => 'error',
                'error' => 400,
                'message' => 'Invalid email format'
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }

        if (strlen($data['password']) < 6) {
            $error = [
                'type' => 'error',
                'error' => 400,
                'message' => 'Password must be at least 6 characters long'
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }
        
        try {
            $patient = $this->servicePatient->registerPatient(
                $data['nom'],
                $data['prenom'],
                $data['date_naissance'],
                $data['adresse'],
                $data['code_postal'],
                $data['ville'],
                $data['email'],
                $data['telephone'],
                $data['password']
            );
            
            $result = [
                'type' => 'success',
                'message' => 'Patient registered successfully',
                'patient' => [
                    'id' => $patient->id,
                    'nom' => $patient->nom,
                    'prenom' => $patient->prenom,
                    'email' => $patient->email,
                    'ville' => $patient->ville
                ],
                'links' => [
                    'signin' => [
                        'href' => '/auth/signin'
                    ]
                ]
            ];
            
            $response->getBody()->write(json_encode($result));
            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'already exists')) {
                $error = [
                    'type' => 'error',
                    'error' => 409,
                    'message' => $e->getMessage()
                ];
                $response->getBody()->write(json_encode($error));
                return $response
                    ->withStatus(409)
                    ->withHeader('Content-Type', 'application/json');
            }
            
            $error = [
                'type' => 'error',
                'error' => 400,
                'message' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}