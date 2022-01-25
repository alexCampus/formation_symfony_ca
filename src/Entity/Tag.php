<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *      paginationItemsPerPage=5,
 *     collectionOperations={"get", "post"},
 *     itemOperations={"get", "delete", "put", "patch"},
 *     normalizationContext={"groups"={"tag:read"}},
 *     denormalizationContext={"groups"={"tag:write"}},
 * )
 * @ORM\Entity(repositoryClass=TagRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"post:read", "tag:read", "tag:write", "user:read"})
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updatedAt;

    /**
     * @Groups("tag:read")
     * @ORM\ManyToMany(targetEntity=Post::class, inversedBy="tags")
     */
    private $posts;

    /**
     * @Groups("tag:read")
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tags")
     */
    private $user;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTimeImmutable());
        $this->setUpdatedAt(new \DateTimeImmutable());
        $this->posts = new ArrayCollection();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        $this->posts->removeElement($post);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
