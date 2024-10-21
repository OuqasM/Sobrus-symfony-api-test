<?php

namespace App\Controller\Api;

namespace App\Controller\Api;

use App\Entity\BlogArticle;
use App\Repository\BlogArticleRepository;
use App\Service\KeywordManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/blog-articles')]
class BlogArticleController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface    $serializer,
        ValidatorInterface     $validator,
    )
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    #[Route('', methods: ['GET'])]
    public function list(BlogArticleRepository $blogArticleRepository): JsonResponse
    {
        $articles = $blogArticleRepository->findAll();
        return $this->json($articles);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function get(int $id, BlogArticleRepository $repository): JsonResponse
    {
        $article = $repository->find($id);
        if (!$article) {
            return new JsonResponse(['error' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($article);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, KeywordManager $keywordManager, SluggerInterface $slugger): JsonResponse
    {
        $blogArticle = $this->serializer->deserialize($request->getContent(), BlogArticle::class, 'json');
    
        $blogArticle->setSlug($slugger->slug($blogArticle->getTitle())->lower());
    
        // Check for banned words in the article content
        $frequentWords = $keywordManager->findMostFrequentWords($blogArticle->getContent());
        if (is_null($frequentWords)) {
            return new JsonResponse(['error' => 'Content contains banned words.'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        $errors = $this->validator->validate($blogArticle);
        if (count($errors) > 0) {
            return $this->json($errors, JsonResponse::HTTP_BAD_REQUEST);
        }
    
        $blogArticle->setKeywords($frequentWords);
    
        $this->entityManager->persist($blogArticle);
        $this->entityManager->flush();
    
        return $this->json($blogArticle, JsonResponse::HTTP_CREATED);
    }
}


