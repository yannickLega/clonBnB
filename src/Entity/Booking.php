<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan("today", message="la date d'arrivée doit etre ultérieure à celle d'aujourd'hui", groups={"front"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan(propertyPath="startDate", message="la date de départ doit etre plus éloignée que la date de d'arrivée")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;


    /**
     * Callback appellé à chaque fois que l'on créé une reservation
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function prePersist() {
        if(empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }

        if(empty($this->amount)) {
            //prix de l'annonce * nombre de jours
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    public function  isBookableDates() {
        // 1- il faut connaitre les dates impossible pour l'annonce
        $notAvailableDays = $this->ad->getNotAvailableDays();
        // 2- il faut comparer les dates choisies avec les dates impossibles
        $bookingDays = $this->getDays();

        $formatDay = function($day) {
            return $day->format('Y-m-d');
        };

        //tableau des chaines de caracteres de mes journées
        $days = array_map($formatDay, $bookingDays);
        $notAvailable = array_map($formatDay, $notAvailableDays);

        foreach($days as $day) {
            if(array_search($day, $notAvailable) !== false) return false;
        }

        return true;
    }

    /**
     * permet de récuperer un tableau des journées qui correspondent à ma reservation
     *
     * @return array un tableau d'objet DateTime representant les jours de la reservation
     */
    public function getDays() {
        $resultat = range(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            24 * 60 * 60
        );

        $days = array_map(function($dayTimestamp) {
            return new \DateTime(date('Y-m-d', $dayTimestamp));
        }, $resultat);

        return $days;
    }

    public function getDuration() {
        $diff = $this->endDate->diff($this->startDate);
        return $diff->days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
