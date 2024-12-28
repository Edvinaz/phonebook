<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['phonebook_read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['phonebook_read', 'phonebook_write'])]
    private ?string $name = null;

    #[ORM\Column(length: 32)]
    #[Groups(['phonebook_read', 'phonebook_write'])]
    private ?string $phone = null;

    #[ORM\ManyToOne(inversedBy: 'phonebooks')]
    #[Groups(['phonebook_read'])]
    private ?User $owner = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'shares')]
    #[Groups(['phonebook_read'])]
    private Collection $shared_with;

    public function __construct()
    {
        $this->shared_with = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getSharedWith(): Collection
    {
        return $this->shared_with;
    }

    public function addSharedWith(User $sharedWith): static
    {
        if (!$this->shared_with->contains($sharedWith)) {
            $this->shared_with->add($sharedWith);
        }

        return $this;
    }

    public function removeSharedWith(User $sharedWith): static
    {
        $this->shared_with->removeElement($sharedWith);

        return $this;
    }
}
