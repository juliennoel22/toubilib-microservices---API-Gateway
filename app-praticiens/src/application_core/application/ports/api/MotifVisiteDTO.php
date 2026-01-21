<?php

namespace toubilib\core\application\ports\api;

class MotifVisiteDTO
{
    public int $id;
    public int $specialiteId;
    public string $libelle;

    public function __construct(int $id, int $specialiteId, string $libelle)
    {
        $this->id = $id;
        $this->specialiteId = $specialiteId;
        $this->libelle = $libelle;
    }
}
