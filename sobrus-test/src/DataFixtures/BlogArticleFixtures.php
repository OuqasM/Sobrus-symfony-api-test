<?php

namespace App\DataFixtures;

use App\Entity\BlogArticle;
use App\Enum\BlogArticleStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class BlogArticleFixtures extends Fixture
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * @return BlogArticleStatus
     */
    private function getRandomStatus(): BlogArticleStatus
    {
        return BlogArticleStatus::cases()[array_rand(BlogArticleStatus::cases())];
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $blogArticle = new BlogArticle();
            
            $title = "Blog Article $i";
            $blogArticle->setAuthorId(mt_rand(1, 5));  // Random author ID
            $blogArticle->setTitle($title);
            $blogArticle->setSlug($this->slugger->slug($title)->lower());
            $blogArticle->setPublicationDate(new \DateTime());
            $blogArticle->setCreationDate(new \DateTime('-1 days'));
            $blogArticle->setContent("This is the content for blog article$i .");
            $blogArticle->setKeywords(['blog', 'article', "keyword$i"]);
            $blogArticle->setStatus($this->getRandomStatus());
            $blogArticle->setCoverPictureRef("/uploads/blog-cover-picture-$i.jpg");

            $manager->persist($blogArticle);
        }

        $manager->flush();
    }
}
