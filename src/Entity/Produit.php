<?php

namespace App\Entity;

use App\Entity\Categorie;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
/**
 * @Vich\Uploadable
 */
class Produit
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: 'integer')]
  private $id;

  #[ORM\Column(type: 'string', length: 255)]
  private $nom;

  #[ORM\Column(type: 'text')]
  private $description;

  #[ORM\Column(type: 'float')]
  private $prix;

  #[ORM\Column(type: 'string', length: 255)]
  private $image;

  #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'produit')]
  #[ORM\JoinColumn(nullable: false)]
  private $categorie;

  /**
   * @Vich\UploadableField(mapping= "produit_images", fileNameProperty= "image")
   */
  private $imageFile;

  #[ORM\Column(type: 'datetime')]
  private $updated_at;




  public function getId(): ?int
  {
    return $this->id;
  }

  public function getNom(): ?string
  {
    return $this->nom;
  }

  public function setNom(string $nom): self
  {
    $this->nom = $nom;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(string $description): self
  {
    $this->description = $description;

    return $this;
  }

  public function getPrix(): ?float
  {
    return $this->prix;
  }

  public function setPrix(float $prix): self
  {
    $this->prix = $prix;

    return $this;
  }

  public function getImage(): ?string
  {
    return $this->image;
  }

  public function setImage(string $image): self
  {
    $this->image = $image;

    return $this;
  }

  public function getCategorie(): ?Categorie
  {
    return $this->categorie;
  }

  public function setCategorie(?Categorie $categorie): self
  {
    $this->categorie = $categorie;

    return $this;
  }

  public function getImageFile(): ?File
  {
    return $this->imageFile;
  }

  public function setImageFile(?File $imageFile = null): self
  // le ? indique que le parametre devant lequel il se trouve peut être null
  {
    $this->imageFile = $imageFile;

    if ($this->imageFile instanceof UploadedFile) {
      $this->updated_at = new \DateTime('now');
      // dans le cas où on upload une image, on modifie la date de mise à jour
    }
    return $this;
  }

  public function getUpdatedAt(): ?\DateTimeInterface
  {
    return $this->updated_at;
  }

  public function setUpdatedAt(\DateTimeInterface $updated_at): self
  {
    $this->updated_at = $updated_at;

    return $this;
  }
}
