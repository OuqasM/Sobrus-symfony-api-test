<?php

namespace App\Controller\Api;

namespace App\Controller\Api;

use App\Entity\BlogArticle;
use App\Enum\BlogArticleStatus;
use App\Repository\BlogArticleRepository;
use App\Service\KeywordService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

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
    public function create(Request $request, KeywordService $keywordService, SluggerInterface $slugger): JsonResponse
    {
        $article = new BlogArticle();

        $article->setAuthorId($request->request->get('authorId'));
        $article->setTitle($request->request->get('title'));
        $article->setPublicationDate(new \DateTime($request->request->get('publicationDate')));
        $article->setCreationDate(new \DateTime($request->request->get('creationDate')));
        $article->setContent($request->request->get('content'));
        $article->setStatus(BlogArticleStatus::from($request->request->get('status')));
        $article->setSlug($slugger->slug($article->getTitle())->lower());
    
        $frequentWords = $keywordService->findMostFrequentWords($article->getContent());
        if (is_null($frequentWords)) {
            return new JsonResponse(['error' => 'Unaccepted article content.'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        $file = $request->files->get('coverPictureRef');
        if ($file) {
            $filename = uniqid() . '.' . $file->guessExtension();
            try {
                $file->move($this->getParameter('uploads_directory'), $filename);
                $article->setCoverPictureRef($filename);
            } catch (Throwable $th) {
                return new JsonResponse(['error' => 'File upload failed.'], JsonResponse::HTTP_BAD_REQUEST);
            }
        }
    
        $errors = $this->validator->validate($article);
        if (count($errors) > 0) {
            return $this->json($errors, JsonResponse::HTTP_BAD_REQUEST);
        }
    
        $article->setKeywords($frequentWords);
    
        $this->entityManager->persist($article);
        $this->entityManager->flush();
    
        return $this->json($article, JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PATCH'])] // accept json body
    public function update(int $id, Request $request, KeywordService $keywordService, SluggerInterface $slugger): JsonResponse
    {
        $article = $this->entityManager->getRepository(BlogArticle::class)->find($id);
    
        if (!$article) {
            return new JsonResponse(['error' => 'BlogArticle not found.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $article->setAuthorId($data['authorId'] ?? $article->getAuthorId());
        $article->setTitle($data['title'] ?? $article->getTitle());
        $article->setPublicationDate(new \DateTime($data['publicationDate'] ?? $article->getPublicationDate()->format('Y-m-d H:i:s')));
        $article->setCreationDate(new \DateTime($data['creationDate'] ?? $article->getCreationDate()->format('Y-m-d H:i:s')));
        $article->setContent($data['content'] ?? $article->getContent());
        $article->setStatus(BlogArticleStatus::from($data['status'] ?? $article->getStatus()->value));
        $article->setSlug($slugger->slug($article->getTitle())->lower());
    
        $frequentWords = $keywordService->findMostFrequentWords($article->getContent());
        if (is_null($frequentWords)) {
            return new JsonResponse(['error' => 'Unaccepted article content.'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        $article->setKeywords($frequentWords);
        $this->entityManager->flush();
    
        return $this->json($article, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $article = $this->entityManager->getRepository(BlogArticle::class)->find($id);
        if (!$article) {
            return new JsonResponse(['error' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }

        $article->setStatus(BlogArticleStatus::DELETED);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}


