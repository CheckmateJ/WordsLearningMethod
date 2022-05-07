<?php

namespace App\Entity;

use App\Repository\LearningPlanRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LearningPlanRepository::class)
 */
class LearningPlan
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Course::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $course;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $limitOnDay;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function getLimitOnDay(): ?int
    {
        return $this->limitOnDay;
    }

    public function setLimitOnDay(int $limitOnDay): void
    {
        $this->limitOnDay = $limitOnDay;
    }
}
