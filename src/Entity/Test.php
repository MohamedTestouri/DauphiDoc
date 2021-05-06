<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Test
 *
 * @ORM\Table(name="test", indexes={@ORM\Index(name="noenseig", columns={"noenseig"})})
 * @ORM\Entity(repositoryClass=App\Repository\TestRepository::class)
 */
class Test
{
    /**
     * @var int
     *
     * @ORM\Column(name="idtest", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idtest;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false)
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cat", type="string", length=50, nullable=true)
     */
    private $cat;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contenu", type="string", length=50, nullable=true)
     */
    private $contenu;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="datetest", type="date", nullable=true)
     */
    private $datetest;

    /**
     * @var string|null
     *
     * @ORM\Column(name="noenseig", type="string", length=25, nullable=true)
     */
    private $noenseig;

    public function getIdtest(): ?int
    {
        return $this->idtest;
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

    public function getCat(): ?string
    {
        return $this->cat;
    }

    public function setCat(?string $cat): self
    {
        $this->cat = $cat;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDatetest(): ?\DateTimeInterface
    {
        return $this->datetest;
    }

    public function setDatetest(?\DateTimeInterface $datetest): self
    {
        $this->datetest = $datetest;

        return $this;
    }

    public function getNoenseig(): ?string
    {
        return $this->noenseig;
    }

    public function setNoenseig(?string $noenseig): self
    {
        $this->noenseig = $noenseig;

        return $this;
    }


}
