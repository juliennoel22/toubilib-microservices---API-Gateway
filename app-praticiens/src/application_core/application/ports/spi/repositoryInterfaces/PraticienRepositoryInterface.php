<?php
namespace toubilib\core\application\ports\spi\repositoryInterfaces;

use toubilib\core\domain\entities\praticien\Praticien;

interface PraticienRepositoryInterface{

    public function GetAllPraticiens() : array;
    public function findPraticien(string $id_p) : Praticien;
    public function findPraticienId(string $id_p) : Praticien;
    public function findPraticienBy(string $type, string $value) : array;
}