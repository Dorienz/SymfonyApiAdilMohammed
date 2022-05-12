<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DepartmentRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
class Department
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
    private $code;


     /**
     * @Groups("region:read")
     */
    #[ORM\Column(type: 'string', length: 255)]
    
    private $nom;



    
    #[ORM\ManyToOne(targetEntity: Region::class, inversedBy: 'departments')]
    #[ORM\JoinColumn(nullable: false)]
    //#[Groups(["region:read"])]   
    private $region;


     /**
     * @Groups("region:read")
     */
    #[ORM\OneToMany(mappedBy: 'department', targetEntity: Commune::class, orphanRemoval: true)]
    //#[Groups(["region:read"])]
    private $communes;

    public function __construct()
    {
        $this->communes = new ArrayCollection();
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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Collection<int, Commune>
     */
    public function getCommunes(): Collection
    {
        return $this->communes;
    }

    public function addCommune(Commune $commune): self
    {
        if (!$this->communes->contains($commune)) {
            $this->communes[] = $commune;
            $commune->setDepartment($this);
        }

        return $this;
    }

    public function removeCommune(Commune $commune): self
    {
        if ($this->communes->removeElement($commune)) {
            // set the owning side to null (unless already changed)
            if ($commune->getDepartment() === $this) {
                $commune->setDepartment(null);
            }
        }

        return $this;
    }
}
