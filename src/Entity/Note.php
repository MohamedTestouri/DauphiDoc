<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Note
 *
 * @ORM\Table(name="note", indexes={@ORM\Index(name="idtest", columns={"nomtest"}), @ORM\Index(name="ideleve", columns={"nomeleve"})})
 * @ORM\Entity(repositoryClass=App\Repository\NoteRepository::class)
 */
class Note
{
    /**
     * @var int
     *
     * @ORM\Column(name="idnote", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idnote;

    /**
     * @var string
     *
     * @ORM\Column(name="nomtest", type="string", length=11, nullable=false)
     */
    private $nomtest;

    /**
     * @var string
     *
     * @ORM\Column(name="nomeleve", type="string", length=11, nullable=false)
     */
    private $nomeleve;

    /**
     * @var int
     *
     * @ORM\Column(name="note", type="integer", nullable=false)
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="resultat", type="string", length=50, nullable=false)
     */
    private $resultat;

    public function getIdnote(): ?int
    {
        return $this->idnote;
    }

    public function getNomtest(): ?string
    {
        return $this->nomtest;
    }

    public function setNomtest(string $nomtest): self
    {
        $this->nomtest = $nomtest;

        return $this;
    }

    public function getNomeleve(): ?string
    {
        return $this->nomeleve;
    }

    public function setNomeleve(string $nomeleve): self
    {
        $this->nomeleve = $nomeleve;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getResultat(): ?string
    {
        return $this->resultat;
    }

    public function setResultat(string $resultat): self
    {
        $this->resultat = $resultat;

        return $this;
    }


}
