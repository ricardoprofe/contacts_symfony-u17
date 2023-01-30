<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 5, options: ["default"=> "Mr."])]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $surname = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'id_contact', targetEntity: Phone::class)]
    private Collection $phones;

    public function __construct()
    {
        $this->phones = new ArrayCollection();
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Phone>
     */
    public function getPhones(): Collection
    {
        return $this->phones;
    }

    public function addPhone(Phone $phone): self
    {
        if (!$this->phones->contains($phone)) {
            $this->phones->add($phone);
            $phone->setIdContact($this);
        }

        return $this;
    }

    public function removePhone(Phone $phone): self
    {
        if ($this->phones->removeElement($phone)) {
            // set the owning side to null (unless already changed)
            if ($phone->getIdContact() === $this) {
                $phone->setIdContact(null);
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        $phoneList = [];
        foreach ($this->phones as $phone) {
            $phoneList[] = [
                'number' => $phone->getNumber(),
                'type' => $phone->getType(),
            ];
        }
        $contactArray = [
            'id' => $this->id,
            'title' => $this->title,
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'birthdate' => $this->birthdate->format('Y-m-d'),
            'phones' => $phoneList,
        ];
        return $contactArray;
    }

    public function fromJson($content): void
    {
        $content = json_decode($content, true);
        $this->title = $content['title'];
        $this->name = $content['name'];
        $this->surname = $content['surname'];
        $this->email = $content['email'];
        $this->birthdate = DateTime::createFromFormat('Y-m-d', $content['birthdate']);
    }
}
