<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
class Inscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $dateInscription = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    private ?sortie $inclus = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    private ?participant $estInscrit = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateInscription(): ?DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getInclus(): ?sortie
    {
        return $this->inclus;
    }

    public function setInclus(?sortie $inclus): self
    {
        $this->inclus = $inclus;

        return $this;
    }

    public function getEstInscrit(): ?participant
    {
        return $this->estInscrit;
    }

    public function setEstInscrit(?participant $estInscrit): self
    {
        $this->estInscrit = $estInscrit;

        return $this;
    }
}
