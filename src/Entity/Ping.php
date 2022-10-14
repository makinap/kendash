<?php

namespace App\Entity;

use App\Repository\PingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PingRepository::class)]
class Ping
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $device = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $service = null;

    #[ORM\Column]
    private ?int $counter = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTimeInterface $registered_on = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $site = null;

    public function __construct()
    {
        $this->registered_on = new \DateTime('now');
    }

    public function __toString() : String
    {
        return $this->getShortService().str_pad($this->counter,"4","0",STR_PAD_LEFT);
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(?string $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getShortService(): string
    {
         return match ($this->service) {
                 "MOBILE" => "MO",
                 "TV" => "TV",
                 "SMART SERVICE" => "SM",
                 default => "SP",
             };
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(?string $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getCounter(): ?int
    {
        return $this->counter;
    }

    public function setCounter(int $counter): self
    {
        $this->counter = $counter;

        return $this;
    }

    public function getRegisteredOn(): ?\DateTimeInterface
    {
        return $this->registered_on;
    }

    public function setRegisteredOn(\DateTimeInterface $registered_on): self
    {
        $this->registered_on = $registered_on;

        return $this;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(?string $site): self
    {
        $this->site = $site;

        return $this;
    }
}
