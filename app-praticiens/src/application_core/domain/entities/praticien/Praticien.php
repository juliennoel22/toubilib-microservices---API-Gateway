<?php

namespace toubilib\core\domain\entities\praticien;


class Praticien
{
private string $id; 
private string $nom; 
private string $prenom; 
private string $ville; 
private string $email; 
private Specialite $specialite;

public function  __construct(
    string $id,
    string $nom,
    string $prenom, 
    string $ville,
    string $email,
    Specialite $specialite
    )
{
    $this->id=$id;
    $this->nom=$nom;
    $this->prenom=$prenom;
    $this->ville=$ville;
    $this->email=$email;
    $this->specialite=$specialite;
}

    public function getId() : string{
        return $this->id;
    }
    public function getNom() : string{
        return $this->nom;
    }
    public function getPrenom() : string{
        return $this->prenom;
    }
    public function getVille() : string{
        return $this->ville;
    }
    public function getEmail() : string{
        return $this->email;
    }
    public function getSpecialite() : Specialite{
        return $this->specialite;
    }


}