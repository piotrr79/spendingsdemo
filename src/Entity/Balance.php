<?php

namespace App\Entity;

use App\Repository\BalanceRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=BalanceRepository::class)
 */
class Balance
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36, nullable=false)
     */
    private $uuid;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="uuid")
     * })
     */
    private $user_id;

    /**
     * @ORM\Column(type="decimal", precision=19, scale=4)
     */
    private $total_debit;

    /**
     * @ORM\Column(type="decimal", precision=19, scale=4)
     */
    private $total_credit;

    /**
     * @ORM\Column(type="decimal", precision=19, scale=4)
     */
    private $balance;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_updated;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->user_id = new ArrayCollection();
        $this->date_updated = new \DateTime();
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

    public function setUserId(User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getTotalDebit(): ?string
    {
        return $this->total_debit;
    }

    public function setTotalDebit(string $total_debit): self
    {
        $this->total_debit = $total_debit;

        return $this;
    }

    public function getTotalCredit(): ?string
    {
        return $this->total_credit;
    }

    public function setTotalCredit(string $total_credit): self
    {
        $this->total_credit = $total_credit;

        return $this;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getDateUpdated(): ?\DateTimeInterface
    {
        return $this->date_updated;
    }

    public function setDateUpdated(\DateTimeInterface $date_updated): self
    {
        $this->date_updated = $date_updated;

        return $this;
    }
}
