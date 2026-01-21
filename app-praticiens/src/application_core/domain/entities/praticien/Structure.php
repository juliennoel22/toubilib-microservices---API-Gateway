<?php

namespace toubilib\core\domain\entities\praticien;

class Structure
{
    private string $id;
    private string $nom;
    private string $adresse;
    private ?string $ville;
    private ?string $codePostal;
    private ?string $telephone;

    public function __construct(
        string $id,
        string $nom,
        string $adresse,
        ?string $ville = null,
        ?string $codePostal = null,
        ?string $telephone = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->adresse = $adresse;
        $this->ville = $ville;
        $this->codePostal = $codePostal;
        $this->telephone = $telephone;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }
}
