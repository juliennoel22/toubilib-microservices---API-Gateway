<?php
namespace toubilib\core\application\ports\spi\repositoryInterfaces;

use toubilib\core\domain\entities\praticien\Patient;

interface PatientRepositoryInterface {
    public function save(Patient $patient): Patient;
    public function findById(string $id): ?Patient;
    public function findByEmail(string $email): ?Patient;
}