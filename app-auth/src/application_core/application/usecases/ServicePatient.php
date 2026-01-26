<?php
namespace toubilib\core\application\usecases;

use Exception;
use toubilib\core\application\ports\api\PatientDTO;
use toubilib\core\application\ports\api\ServicePatientInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\PatientRepositoryInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\UserRepositoryInterface;
use toubilib\core\domain\entities\auth\User;
use Ramsey\Uuid\Uuid;
use toubilib\core\domain\entities\praticien\Patient;

class ServicePatient implements ServicePatientInterface {
    private PatientRepositoryInterface $patientRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        PatientRepositoryInterface $patientRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->patientRepository = $patientRepository;
        $this->userRepository = $userRepository;
    }

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
    ): PatientDTO {
        
        $existingPatient = $this->patientRepository->findByEmail($email);
        if ($existingPatient !== null) {
            throw new Exception("Patient déja existant avec cet email");
        }

        $existingUser = $this->userRepository->FindByEmail($email);
        if ($existingUser !== null) {
            throw new Exception("Utilisateur avec cet email déja existant");
        }

        $patientId = Uuid::uuid4()->toString();
        
        $patient = new Patient(
            $patientId,
            $nom,
            $prenom,
            $date_naissance,
            $adresse,
            $code_postal,
            $ville,
            $email,
            $telephone
        );

        $savedPatient = $this->patientRepository->save($patient);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user = new User(
            $patientId,
            $email,
            $hashedPassword,
            '5'
        );
        
        $this->userRepository->save($user);

        return new PatientDTO(
            $savedPatient->getId(),
            $savedPatient->getNom(),
            $savedPatient->getPrenom(),
            $savedPatient->getDateNaissance(),
            $savedPatient->getAdresse(),
            $savedPatient->getCodePostal(),
            $savedPatient->getVille(),
            $savedPatient->getEmail(),
            $savedPatient->getTelephone()
        );
    }
}