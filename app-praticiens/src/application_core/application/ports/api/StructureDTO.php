<?php

namespace toubilib\core\application\ports\api;

class StructureDTO
{
    public string $id;
    public string $nom;
    public string $adresse;
    public ?string $ville;
    public ?string $codePostal;
    public ?string $telephone;

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
}
