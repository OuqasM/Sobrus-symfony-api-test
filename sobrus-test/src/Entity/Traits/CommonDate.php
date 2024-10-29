<?php
namespace App\Entity\Traits;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
trait CommonDate
{

    public function setAuthor(?string $author = null)
    {
        $this->author = $author;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateDate()
    {
        $this->author = 'Mr. ' . $this->author;
    }
}