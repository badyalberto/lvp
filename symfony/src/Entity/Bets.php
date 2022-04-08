<?php

namespace App\Entity;

use App\Repository\BetsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BetsRepository::class)
 */
class Bets
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank(message="The value is not a valid")
     * @Assert\Type(
     *     type="integer",
     *     message="The value is not a valid."
     * )
     */
    private $kills;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="id_bet")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="The user cannot be blank")
     */
    private $id_user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKills(): ?int
    {
        return $this->kills;
    }

    public function setKills(int $kills): self
    {
        $this->kills = $kills;

        return $this;
    }

    public function getIdUser(): ?Users
    {
        return $this->id_user;
    }

    public function setIdUser(?Users $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }
}
