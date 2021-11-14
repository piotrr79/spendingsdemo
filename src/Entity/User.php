<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36, nullable=false)
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=36, unique=true)
     */
    private $user_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_created;

    /**
     * @ORM\OneToOne(targetEntity=Thershold::class, mappedBy="user_id", cascade={"persist", "remove"})
     */
    private $thershold;

    /**
     * @ORM\OneToOne(targetEntity=Balance::class, mappedBy="user_id", cascade={"persist", "remove"})
     */
    private $balance;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->date_created = new \DateTime();
    }

    public function getUuid(): mixed
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->user_id;
    }

    public function setUserId(string $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->date_created;
    }

    public function setDateCreated(\DateTimeInterface $date_created): self
    {
        $this->date_created = $date_created;

        return $this;
    }

    public function getThershold(): ?Thershold
    {
        return $this->thershold;
    }

    public function getBalance(): ?Balance
    {
        return $this->balance;
    }
}
