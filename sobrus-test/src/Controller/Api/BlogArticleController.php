<?php

namespace App\Controller\Api;

namespace App\Controller\Api;

use App\Repository\BlogArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/blog-articles')]
class BlogArticleController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        $this->entityManager = $entityManager;
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
}


