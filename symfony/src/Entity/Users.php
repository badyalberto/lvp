<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 */
class Users
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Email(
     *     message = "The email is not a valid email."
     * )
     * @Assert\NotBlank(message="The email cannot be blank")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="The name cannot be blank")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Bets::class, mappedBy="id_user")
     */
    private $id_bet;

    public function __construct()
    {
        $this->id_bet = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
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

    /**
     * @return Collection<int, Bets>
     */
    public function getIdBet(): Collection
    {
        return $this->id_bet;
    }

    public function addIdBet(Bets $idBet): self
    {
        if (!$this->id_bet->contains($idBet)) {
            $this->id_bet[] = $idBet;
            $idBet->setIdUser($this);
        }

        return $this;
    }

    public function removeIdBet(Bets $idBet): self
    {
        if ($this->id_bet->removeElement($idBet)) {
            // set the owning side to null (unless already changed)
            if ($idBet->getIdUser() === $this) {
                $idBet->setIdUser(null);
            }
        }

        return $this;
    }
}
