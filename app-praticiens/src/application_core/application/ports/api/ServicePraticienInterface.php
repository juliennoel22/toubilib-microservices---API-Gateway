<?php
namespace toubilib\core\application\ports\api;

interface ServicePraticienInterface{

    public function ListerPraticiens(): array;
    public function ListerPraticien(string $nom): PraticienDTO;
    public function ListerPraticienId(string $id): PraticienDTO;
    public function RecherchePraticiens(string $type, string $value): array;
}




