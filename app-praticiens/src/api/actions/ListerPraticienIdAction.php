<?php

namespace toubilib\api\actions;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use toubilib\core\application\ports\api\ServicePraticienInterface;

class ListerPraticienIdAction
{
    private ServicePraticienInterface $servicepraticien;
    public function __construct(ServicePraticienInterface $servicepraticien){
        $this->servicepraticien = $servicepraticien;
    }
    public function __invoke(Request $req, Response $res)
    {
        $id = $req->getAttribute('id');
        $prat = $this->servicepraticien->ListerPraticienId($id); 
        $res->getBody()->write(json_encode($prat));
        return $res->withHeader('Content-Type','application/json');
    }
}