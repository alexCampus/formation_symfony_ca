<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     paginationItemsPerPage=5,
 *     collectionOperations={"get",
 *      "post",
 *     "post_excel"={
 *          "route_name"="api_export_post",
 *          "openapi_context"={
 *              "summary"="Export Excel des posts",
 *              "description"="# Pop a great rabbit picture by color!\n\n![A great rabbit](https://rabbit.org/graphics/fun/netbunnies/jellybean1-brennan1.jpg)",
 *              "parameters"={
 *              {
 *                  "name"="nom",
 *                  "in"="query",
 *                  "description"="Nom d'un post",
 *                  "required"=false,
 *                  "type"="text"
 *              },{
 *                  "name"="auteur",
 *                  "in"="query",
 *                  "description"="Nom d'un auteur",
 *                  "required"=false,
 *                  "type"="text"
 *              }
 *     }
 *     }}
 *     },
 *     itemOperations={"get",
 *      "delete"={"security"="is_granted('POST_DELETE', object)"},
 *      "put"={"security"="is_granted('POST_EDIT', object)"},
 *     "patch"={"security"="is_granted('POST_EDIT', object)"}
 * },
 *     normalizationContext={"groups"={"post:read"}},
 *     denormalizationContext={"groups"={"post:write"}},
 * )
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"post:read", "comment:read"})
     */
    private $id;

    /**
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"post:read", "post:write", "comment:read", "tag:read", "user:read"})
     */
    private $name;

    /**
     * @Groups({"post:read", "post:write", "comment:read", "tag:read", "user:read"})
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="datetime_immutable")
     */
    private $updatedAt;

    /**
     * @Groups({"post:read"})
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="post", orphanRemoval=true)
     */
    private $comments;

    /**
     * @Groups({"post:read"})
     * @ORM\ManyToMany(targetEntity=Tag::class, mappedBy="posts")
     */
    private $tags;

    /**
     * @Groups({"post:read", "post:write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageName;

    /**
     * @Groups({"post:read", "tag:read", "comment:read", "post:write"})
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     */
    private $user;

    public function __construct()
    {
//        $this->setCreatedAt(new \DateTimeImmutable());
//        $this->setUpdatedAt(new \DateTimeImmutable());
        $this->comments = new ArrayCollection();
        $this->tags     = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

//    /**
//     * @ORM\PreUpdate()
//     */
//    public function setUpdatedValue()
//    {
//        $this->setUpdatedAt(new \DateTimeImmutable());
//    }

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addPost($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removePost($this);
        }

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        if ($imageName !== null) {
            $this->imageName = $imageName;
        }

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
