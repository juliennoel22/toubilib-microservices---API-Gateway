<?php
namespace toubilib\core\application\ports\api;

class PatientDTO {
    public string $id;
    public string $nom;
    public string $prenom;
    public string $date_naissance;
    public string $adresse;
    public string $code_postal;
    public string $ville;
    public string $email;
    public string $telephone;

    public function __construct(
        string $id,
        string $nom,
        string $prenom,
        string $date_naissance,
        string $adresse,
        string $code_postal,
        string $ville,
        string $email,
        string $telephone
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->date_naissance = $date_naissance;
        $this->adresse = $adresse;
        $this->code_postal = $code_postal;
        $this->ville = $ville;
        $this->email = $email;
        $this->telephone = $telephone;
    }
}