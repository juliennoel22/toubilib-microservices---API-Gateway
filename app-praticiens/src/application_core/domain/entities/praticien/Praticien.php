<?php

namespace toubilib\core\domain\entities\praticien;


class Praticien
{
    private string $id; 
    private string $nom; 
    private string $prenom; 
    private string $ville; 
    private string $email;
    private string $telephone;
    private Specialite $specialite;
    private ?Structure $structure;
    private ?string $rppsId;
    private bool $organisation;
    private bool $nouveauPatient;
    private string $titre;
    private array $moyensPaiement;
    private array $motifsVisite; 
    
    public function  __construct(
        string $id,
        string $nom,
        string $prenom, 
        string $ville,
        string $email,
        string $telephone,
        Specialite $specialite,
        ?Structure $structure = null,
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
        $this->specialite = $specialite;
        $this->structure = $structure;
        $this->rppsId = $rppsId;
        $this->organisation = $organisation;
        $this->nouveauPatient = $nouveauPatient;
        $this->titre = $titre;
        $this->moyensPaiement = $moyensPaiement;
        $this->motifsVisite = $motifsVisite;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getVille(): string
    {
        return $this->ville;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function getSpecialite(): Specialite
    {
        return $this->specialite;
    }

    public function getStructure(): ?Structure
    {
        return $this->structure;
    }

    public function getRppsId(): ?string
    {
        return $this->rppsId;
    }

    public function isOrganisation(): bool
    {
        return $this->organisation;
    }

    public function accepteNouveauPatient(): bool
    {
        return $this->nouveauPatient;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getMoyensPaiement(): array
    {
        return $this->moyensPaiement;
    }

    public function getMotifsVisite(): array
    {
        return $this->motifsVisite;
    }
}