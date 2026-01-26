<?php
namespace toubilib\core\application\ports\api;

interface ServicePatientInterface {
    public function registerPatient(
        string $nom,
        string $prenom,
        string $date_naissance,
        string $adresse,
        string $code_postal,
        string $ville,
        string $email,
        string $telephone,
        string $password
    ): PatientDTO;
}