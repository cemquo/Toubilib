<?php

namespace toubilib\core\application\ports\api\dtos;

class PraticienDTO
{
    public string $id;
    public string $nom;
    public string $prenom;
    public string $specialite;
    public string $ville;
    public string $email;

    public function __construct(string $id, string $nom, string $prenom, string $specialite, string $ville, string $email)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->specialite = $specialite;
        $this->ville = $ville;
        $this->email = $email;
    }
}
