<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\DebitRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=DebitRepository::class)
 */
class Debit
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36, nullable=false)
     */
    private $uuid;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="uuid")
     * })
     */
    private $user_id;

    /**
     * @ORM\Column(type="decimal", precision=19, scale=4)
     */
    private $debit;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_created;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->user_id = new ArrayCollection();
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

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getDebit(): ?string
    {
        return $this->debit;
    }

    public function setDebit(string $debit): self
    {
        $this->debit = $debit;

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
}
