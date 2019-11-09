<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\DateTime;


/**
 * @ORM\Entity(repositoryClass="App\Repository\PlaceRepository")
 */
class Place
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("places_get")
     * @Groups("city_get")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("places_get")
     * @Groups("city_get")
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Your name must be at least {{ limit }} characters long",
     *      maxMessage = "Your name cannot be longer than {{ limit }} characters"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("places_get")
     * @Groups("city_get")
     * @Assert\Length(
     *      min = 5,
     *      max = 100,
     *      minMessage = "Your adress must be at least {{ limit }} characters long",
     *      maxMessage = "Your adress cannot be longer than {{ limit }} characters"
     * )
     */
    private $adress;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups("places_get")
     * @Groups("city_get")
     */
    private $schedule;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups("places_get")
     * @Groups("city_get")
     */
    private $complementInfo;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("places_get")
     * @Groups("city_get")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("places_get")
     * @Groups("city_get")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     * @Groups("places_get")
     * @Groups("city_get")
     */
    private $validate = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review", mappedBy="place", orphanRemoval=true)
     * @Groups("places_get")
     * @Groups("reviews_get")
     */
    private $reviews;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="places")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("places_get")
     */
    private $city;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sport", inversedBy="places")
     */
    private $sport;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("places_get")
     */
    private $longitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("places_get")
     */
    private $latitude;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->sport = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function setSchedule(?string $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getComplementInfo(): ?string
    {
        return $this->complementInfo;
    }

    public function setComplementInfo(?string $complementInfo): self
    {
        $this->complementInfo = $complementInfo;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getValidate(): ?bool
    {
        return $this->validate;
    }

    public function setValidate(bool $validate): self
    {
        $this->validate = $validate;

        return $this;
    }

    /**
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setPlace($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getPlace() === $this) {
                $review->setPlace(null);
            }
        }

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection|Sport[]
     */
    public function getSport(): Collection
    {
        return $this->sport;
    }

    public function addSport(Sport $sport): self
    {
        if (!$this->sport->contains($sport)) {
            $this->sport[] = $sport;
        }

        return $this;
    }

    public function removeSport(Sport $sport): self
    {
        if ($this->sport->contains($sport)) {
            $this->sport->removeElement($sport);
        }

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }
}
