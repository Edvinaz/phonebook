<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user_read', 'phonebook_read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user_read', 'phonebook_read'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Contact>
     */
    #[ORM\OneToMany(targetEntity: Contact::class, mappedBy: 'owner')]
    #[Groups(['user_read'])]
    private Collection $contacts;

    /**
     * @var Collection<int, Contact>
     */
    #[ORM\ManyToMany(targetEntity: Contact::class, mappedBy: 'shared_with')]
    #[Groups(['user_read'])]
    private Collection $shares;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->shares = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addPhonebook(Contact $phonebook): static
    {
        if (!$this->contacts->contains($phonebook)) {
            $this->contacts->add($phonebook);
            $phonebook->setOwner($this);
        }

        return $this;
    }

    public function removePhonebook(Contact $phonebook): static
    {
        if ($this->contacts->removeElement($phonebook)) {
            // set the owning side to null (unless already changed)
            if ($phonebook->getOwner() === $this) {
                $phonebook->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getShares(): Collection
    {
        return $this->shares;
    }

    public function addShare(Contact $share): static
    {
        if (!$this->shares->contains($share)) {
            $this->shares->add($share);
            $share->addSharedWith($this);
        }

        return $this;
    }

    public function removeShare(Contact $share): static
    {
        if ($this->shares->removeElement($share)) {
            $share->removeSharedWith($this);
        }

        return $this;
    }
}
