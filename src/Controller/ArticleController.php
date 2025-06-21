<?php

namespace App\Controller;

use App\Entity\Comment;  
use App\Form\TextType;
use App\Entity\Article;
use App\Form\ArticleForm;
use App\Form\CommentForm;
use App\Form\SearchArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/article')]
final class ArticleController extends AbstractController
{
// src/Controller/ArticleController.php
    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(SearchArticleType::class);
        $form->handleRequest($request);

        $articles = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $articles = $articleRepository->search(
                $data['query'],
                $data['category'],
                $data['tags']
            );
        } else {
            $articles = $articleRepository->findBy(['isPublished' => true], ['createdAt' => 'DESC']);
        }

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'searchForm' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_EDITEUR')]
    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // L'auteur est l'utilisateur connecté
            $article->setAuteur($this->getUser());

            // Date de création
            $article->setCreatedAt(new \DateTime());

            // Gestion de l'image uploadée
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );

                $article->setImage($newFilename);
            }

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
            'edit_mode' => false
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'app_article_show', methods: ['GET', 'POST'])]
    public function show(
        Article $article, 
        CommentRepository $commentRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {
        $comments = $commentRepository->findBy(
            ['article' => $article],
            ['createdAt' => 'DESC']
        );

        $comment = new Comment();
        $form = $this->createForm(CommentForm::class, $comment);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Associe automatiquement l'utilisateur connecté
            if ($user = $security->getUser()) {
                $comment->setUser($user);
            }
            
            $comment->setArticle($article);
            $entityManager->persist($comment);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'comments' => $comments,
            'commentForm' => $form->createView(),
        ]);
    }
    
    #[IsGranted('ROLE_EDITEUR')]
    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
            'edit_mode' => true
        ]);
    }

    #[IsGranted('ROLE_EDITEUR')]
    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
