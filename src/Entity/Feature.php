<?php

namespace App\Entity;

use App\Repository\FeatureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FeatureRepository::class)
 */
class Feature
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class, inversedBy="features")
     */
    private $feature_product;

    public function __construct()
    {
        $this->feature_product = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Product>
     */
    public function getFeatureProduct(): Collection
    {
        return $this->feature_product;
    }

    public function addFeatureProduct(Product $featureProduct): self
    {
        if (!$this->feature_product->contains($featureProduct)) {
            $this->feature_product[] = $featureProduct;
        }

        return $this;
    }

    public function removeFeatureProduct(Product $featureProduct): self
    {
        $this->feature_product->removeElement($featureProduct);

        return $this;
    }
}
