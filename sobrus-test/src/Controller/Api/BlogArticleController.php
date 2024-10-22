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
            return new JsonResponse(['error' => 'Content contains banned words.'], JsonResponse::HTTP_BAD_REQUEST);
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
}


