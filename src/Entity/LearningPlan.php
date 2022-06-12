<?php

namespace App\Entity;

use App\Repository\LearningPlanRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @ORM\OneToOne(targetEntity=Course::class, cascade={"persist", "remove"}, )
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $course;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $limitOnDay;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $currentCardLearnt;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

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

    public function getCurrentCardLearnt(): ?int
    {
        return $this->currentCardLearnt;
    }

    public function setCurrentCardLearnt(?int $currentCardLearnt): self
    {
        $this->currentCardLearnt = $currentCardLearnt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
