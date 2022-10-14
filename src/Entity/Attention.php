<?php

namespace App\Entity;

use App\Repository\AttentionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttentionRepository::class)]
class Attention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Ping $ping = null;

    #[ORM\Column(length: 255)]
    private ?string $agent = null;

    #[ORM\Column(length: 1)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTimeInterface $registered_on = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $attented_on = null;

    public function __construct()
    {
        $this->registered_on = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPing(): ?Ping
    {
        return $this->ping;
    }

    public function setPing(?Ping $ping): self
    {
        $this->ping = $ping;

        return $this;
    }

    public function getAgent(): ?string
    {
        return $this->agent;
    }

    public function setAgent(string $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getAttentedOn(): ?\DateTimeInterface
    {
        return $this->attented_on;
    }

    public function setAttentedOn(?\DateTimeInterface $attented_on): self
    {
        $this->attented_on = $attented_on;

        return $this;
    }
}
