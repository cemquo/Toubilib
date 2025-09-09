<?php

namespace toubilib\core\domain\entities\praticien;

use Ramsey\Uuid\Uuid;

class Praticien
{
    private string $id;
    private string $nom;
    private string $prenom;
    private string $specialite;
    private string $ville;
    private string $email;
    private string $telephone;
    private string $motif_visite;
    private string $moyen_payement;

    public function __construct(string $nom, string $prenom, string $specialite, string $ville, string $email, string $telephone, string $motif_visite, string $moyen_payement)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->specialite = $specialite;
        $this->ville = $ville;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->motif_visite = $motif_visite;
    }

    

}