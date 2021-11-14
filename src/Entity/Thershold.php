<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\ThersholdRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=ThersholdRepository::class)
 */
class Thershold
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
    private $thershold;

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

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getThershold(): ?string
    {
        return $this->thershold;
    }

    public function setThershold(string $thershold): self
    {
        $this->thershold = $thershold;

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
