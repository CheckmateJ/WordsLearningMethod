<?php

namespace App\Entity;

use App\Repository\GrammarRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GrammarRepository::class)
 */
class Grammar
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $tense;

    /**
     * @ORM\Column(type="text")
     */
    private $negativeSentences;

    /**
     * @ORM\Column(type="text")
     */
    private $affirmativeSenteces;

    /**
     * @ORM\Column(type="text")
     */
    private $interrogativeSentences;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $whenToUse;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $other;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTense(): ?string
    {
        return $this->tense;
    }

    public function setTense(string $tense): self
    {
        $this->tense = $tense;

        return $this;
    }

    public function getNegativeSentences(): ?string
    {
        return $this->negativeSentences;
    }

    public function setNegativeSentences(string $negativeSentences): self
    {
        $this->negativeSentences = $negativeSentences;

        return $this;
    }

    public function getAffirmativeSenteces(): ?string
    {
        return $this->affirmativeSenteces;
    }

    public function setAffirmativeSenteces(string $affirmativeSenteces): self
    {
        $this->affirmativeSenteces = $affirmativeSenteces;

        return $this;
    }

    public function getInterrogativeSentences(): ?string
    {
        return $this->interrogativeSentences;
    }

    public function setInterrogativeSentences(string $interrogativeSentences): self
    {
        $this->interrogativeSentences = $interrogativeSentences;

        return $this;
    }

    public function getWhenToUse(): ?string
    {
        return $this->whenToUse;
    }

    public function setWhenToUse(?string $whenToUse): self
    {
        $this->whenToUse = $whenToUse;

        return $this;
    }

    public function getOther(): ?string
    {
        return $this->other;
    }

    public function setOther(?string $other): self
    {
        $this->other = $other;

        return $this;
    }
}
