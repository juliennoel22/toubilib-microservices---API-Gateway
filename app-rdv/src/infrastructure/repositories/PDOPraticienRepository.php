<?php

namespace toubilib\infra\repositories;

use Exception;
use toubilib\core\application\ports\spi\repositoryinterfaces\PraticienRepositoryInterface;
use toubilib\core\domain\entities\praticien\Praticien;
use toubilib\core\domain\entities\praticien\Specialite;

class PDOPraticienRepository implements PraticienRepositoryInterface
{


    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }
    public function getAllPraticiens() : array{
        $statement = $this->pdo->prepare("SELECT p.id, p.nom, p.prenom, p.ville, p.email, s.id as sp_id , s.libelle as sp_libelle
         , s.description as sp_description 
         from praticien p
        join specialite s on p.specialite_id = s.id
        ");
        $statement->execute();
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $praticiens = [];
        foreach ($results as $res){
            $praticiens[] = new Praticien($res['id'],
            $res['nom'],
            $res['prenom'],
            $res['ville'],
            $res['email'],
            new Specialite(
                $res['sp_id'],
                $res['sp_libelle'],
                $res['sp_description']
            )
            );
        }
        return $praticiens;
    }
    public function findPraticien(string $id_p) : Praticien{
     $statement = $this->pdo->prepare("SELECT p.id, p.nom, p.prenom, p.ville, p.email, s.id as sp_id , s.libelle as sp_libelle
         , s.description as sp_description 
         from praticien p
        join specialite s on p.specialite_id = s.id
        where p.nom = :pid
        ");
        
        $statement->execute([":pid"=>$id_p]);
        $results=$statement->fetch(\PDO::FETCH_ASSOC);
        if(!$results){
           throw new Exception("Praticien not found");
        }        
        $praticien = new Praticien(
            $results['id'],
            $results['nom'],
            $results['prenom'],
            $results['ville'],
            $results['email'],
            new Specialite(
                $results['sp_id'],
                $results['sp_libelle'],
                $results['sp_description']
            ));
        return $praticien;
    }
    public function findPraticienId(string $id_p) : Praticien{
     $statement = $this->pdo->prepare("SELECT p.id, p.nom, p.prenom, p.ville, p.email, s.id as sp_id , s.libelle as sp_libelle
         , s.description as sp_description 
         from praticien p
        join specialite s on p.specialite_id = s.id
        where p.id = :pid
        ");
        
        $statement->execute([":pid"=>$id_p]);
        $results=$statement->fetch(\PDO::FETCH_ASSOC);
        if(!$results){
           throw new Exception("Praticien not found");
        }        
        $praticien = new Praticien(
            $results['id'],
            $results['nom'],
            $results['prenom'],
            $results['ville'],
            $results['email'],
            new Specialite(
                $results['sp_id'],
                $results['sp_libelle'],
                $results['sp_description']
            )
            );
       
        return $praticien;
        
    }
    public function findPraticienBy(string $type, string $value) : array{
        if($type=="ville"){
$statement = $this->pdo->prepare("SELECT p.id, p.nom, p.prenom, p.ville, p.email, s.id as sp_id , s.libelle as sp_libelle
         , s.description as sp_description 
         from praticien p
        join specialite s on p.specialite_id = s.id
        where p.ville = :pville
        ");
        
        $statement->execute([":pville"=>$value]);
        $results_a=$statement->fetchAll(\PDO::FETCH_ASSOC);
        // if(!$results){
        //    throw new Exception("Praticien not found");
        // }       
        $praticiens = [];
        foreach($results_a as $results){
             $praticiens [] = new Praticien(
            $results['id'],
            $results['nom'],
            $results['prenom'],
            $results['ville'],
            $results['email'],
            new Specialite(
                $results['sp_id'],
                $results['sp_libelle'],
                $results['sp_description']
            ));
        } 
        return $praticiens;
        }
        else if($type=="specialite"){
          $statement = $this->pdo->prepare("SELECT p.id, p.nom, p.prenom, p.ville, p.email, s.id as sp_id , s.libelle as sp_libelle
         , s.description as sp_description 
         from praticien p
        join specialite s on p.specialite_id = s.id
        where s.libelle = :libelle
        ");
        
        $statement->execute([":libelle"=>$value]);
        $results_a=$statement->fetchAll(\PDO::FETCH_ASSOC);
        // if(!$results){
        //    throw new Exception("Praticien not found");
        // }       
        $praticiens = [];
        foreach($results_a as $results){
             $praticiens [] = new Praticien(
            $results['id'],
            $results['nom'],
            $results['prenom'],
            $results['ville'],
            $results['email'],
            new Specialite(
                $results['sp_id'],
                $results['sp_libelle'],
                $results['sp_description']
            ));
        }
        return $praticiens;
    }
        else{
            throw new Exception("Erreur");
        }
    }
 
}