<?php
namespace toubilib\core\application\ports\api;


class PraticienDTO {

public string $id; 
public string $nom; 
public string $prenom; 
public string $ville; 
public string $email; 
public string $specialite_lib;
public string $specialite_desc;

public function __construct(
 string $id,
 string $nom, 
 string $prenom, 
 string $ville, 
 string $email, 
 string $specialite_lib,
 string $specialite_desc
)
{
    $this->id = $id;
    $this->nom = $nom;
    $this->prenom = $prenom;
    $this->ville = $ville;
    $this->email = $email;
    $this->specialite_lib = $specialite_lib;
    $this->specialite_desc = $specialite_desc;
}





}

