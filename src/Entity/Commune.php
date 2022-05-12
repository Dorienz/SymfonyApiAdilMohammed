<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommuneRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommuneRepository::class)]
class Commune
{
   
     /**
     * @Groups("region:read")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    //#[Groups(["region:read"])]
    private $id;

    
     /**
     * @Groups("region:read")
     */
    #[ORM\Column(type: 'string', length: 255)]
    //#[Groups(["region:read"])]
    private $nom;

     /**
     * @Groups("region:read")
     */
    #[ORM\Column(type: 'string', length: 255)]
    //#[Groups(["region:read"])]
    private $code;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'communes')]
    #[ORM\JoinColumn(nullable: false)]
    //#[Groups(["region:read"])]
    private $department;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }
}
