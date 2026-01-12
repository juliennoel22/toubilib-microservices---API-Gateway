<?php

namespace toubilib\api\actions;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use toubilib\core\application\ports\api\ServicePraticienInterface;

class ListerPraticiensAction
{

private ServicePraticienInterface $servicepraticien;

public function __construct(ServicePraticienInterface $servicepraticien)
{
    $this->servicepraticien = $servicepraticien;
}
public function __invoke(Request $request , Response $response) : Response
{
    $praticiens = $this->servicepraticien->ListerPraticiens();
    $response->getBody()->write(json_encode($praticiens));
    return $response->withHeader('Content-Type','application/json');
    
}




}