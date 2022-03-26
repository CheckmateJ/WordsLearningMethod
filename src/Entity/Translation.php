<?php

namespace App\Entity;

use App\Repository\TranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TranslationRepository::class)
 */
class Translation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"show_flashcard"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Course::class, inversedBy="translations")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"show_flashcard"})
     */
    private $course;

    /**
     * @ORM\Column(type="text")
     * @Groups({"show_flashcard"})
     */
    private $frontSide;

    /**
     * @ORM\Column(type="text")
     * @Groups({"show_flashcard"})
     */
    private $backSide;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"show_flashcard"})
     */
    private $repetition = 0;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"show_flashcard"})
     */
    private $nextRepetition;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function getFrontSide(): ?string
    {
        return $this->frontSide;
    }

    public function setFrontSide(string $frontSide): self
    {
        $this->frontSide = $frontSide;

        return $this;
    }

    public function getBackSide(): ?string
    {
        return $this->backSide;
    }

    public function setBackSide(string $backSide): self
    {
        $this->backSide = $backSide;

        return $this;
    }

    public function getRepetition(): ?int
    {
        return $this->repetition;
    }

    public function setRepetition(int $repetition): self
    {
        $this->repetition = $repetition;

        return $this;
    }

    public function getNextRepetition(): ?\DateTimeInterface
    {
        return $this->nextRepetition;
    }

    public function setNextRepetition(?\DateTimeInterface $nextRepetition): self
    {
        $this->nextRepetition = $nextRepetition;

        return $this;
    }
}
