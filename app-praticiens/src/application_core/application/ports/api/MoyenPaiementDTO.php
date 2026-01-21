<?php

namespace toubilib\core\application\ports\api;

class MoyenPaiementDTO
{
    public int $id;
    public string $libelle;

    public function __construct(int $id, string $libelle)
    {
        $this->id = $id;
        $this->libelle = $libelle;
    }
}
