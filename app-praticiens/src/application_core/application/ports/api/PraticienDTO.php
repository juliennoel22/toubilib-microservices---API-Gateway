<?php
namespace toubilib\core\application\ports\api;


class PraticienDTO {

    public string $id; 
    public string $nom; 
    public string $prenom; 
    public string $ville; 
    public string $email;
    public string $telephone;
    public string $specialite_lib;
    public string $specialite_desc;
    public ?StructureDTO $structure;
    public ?string $rppsId;
    public bool $organisation;
    public bool $nouveauPatient;
    public string $titre;
    public array $moyensPaiement; 
    public array $motifsVisite;

    public function __construct(
        string $id,
        string $nom, 
        string $prenom, 
        string $ville, 
        string $email,
        string $telephone,
        string $specialite_lib,
        string $specialite_desc,
        ?StructureDTO $structure = null,
        ?string $rppsId = null,
        bool $organisation = false,
        bool $nouveauPatient = true,
        string $titre = 'Dr.',
        array $moyensPaiement = [],
        array $motifsVisite = []
    )
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->ville = $ville;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->specialite_lib = $specialite_lib;
        $this->specialite_desc = $specialite_desc;
        $this->structure = $structure;
        $this->rppsId = $rppsId;
        $this->organisation = $organisation;
        $this->nouveauPatient = $nouveauPatient;
        $this->titre = $titre;
        $this->moyensPaiement = $moyensPaiement;
        $this->motifsVisite = $motifsVisite;
    }
}

