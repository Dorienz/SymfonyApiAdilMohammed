<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RegionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: RegionRepository::class)]

/**
 * @UniqueEntity(
 *  fields={"code"},
 *  message="Ce code est déjà utilisé."
 * )
 */

class Region
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]  
    #[Groups(["region:read"])]
    private $id;

    /**
     * @Assert\NotBlank (message="Le code est obligatoire!")
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["region:read"])]
    private $code;

    /**
     * @Assert\NotBlank (message="Le nom est obligatoire")
     */

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["region:read"])]
    private $nom;

    #[ORM\OneToMany(mappedBy: 'region', targetEntity: Department::class, orphanRemoval: true)]
    #[Groups(["region:read"])]
    private $departments;

    public function __construct()
    {
        $this->departments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Department>
     */
    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): self
    {
        if (!$this->departments->contains($department)) {
            $this->departments[] = $department;
            $department->setRegion($this);
        }

        return $this;
    }

    public function removeDepartment(Department $department): self
    {
        if ($this->departments->removeElement($department)) {
            // set the owning side to null (unless already changed)
            if ($department->getRegion() === $this) {
                $department->setRegion(null);
            }
        }

        return $this;
    }
}
