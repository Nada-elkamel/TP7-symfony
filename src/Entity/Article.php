<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min : 5,max:50 , minMessage: "Le nom d'un article doit comporter au moins {{limit }} caractères ", maxMessage:"Le nom d'un article doit comporter au plus {{limit }} caractères")]
    private ?string $nom = null;
/**
    
     *@Assert\NotEqualTo(
     *     value = 0,
     *     message = "Le prix d’un article ne doit pas être égal à  0 "
     * )
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    #[Assert\NotEqualTo(value:0, message:"Le prix d'un article ne doit pas etre égal à 0")]
    private ?string $prix = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?Category $category = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(?string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    
}