<?php

namespace toubilib\api\actions;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use toubilib\core\application\ports\api\ServicePraticienInterface;

class ListerPraticienAction
{
    private ServicePraticienInterface $servicepraticien;
    public function __construct(ServicePraticienInterface $servicepraticien){
        $this->servicepraticien = $servicepraticien;
    }
    public function __invoke(Request $req, Response $res)
    {
        $nom = $req->getAttribute('nom');
        $prat = $this->servicepraticien->ListerPraticien($nom); 
        $res->getBody()->write(json_encode($prat));
        return $res->withHeader('Content-Type','application/json');
    }
}