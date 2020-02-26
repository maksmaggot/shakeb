<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Cocur\Slugify\Slugify;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{

    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @var SlugifyInterface
     */
    private $slugify;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * PostsController constructor.
     * @param PostRepository $postRepository
     * @param EntityManagerInterface $em
     * @param SlugifyInterface $slugify
     */
    public function __construct(PostRepository $postRepository, EntityManagerInterface $em, SlugifyInterface $slugify)
    {
        $this->postRepository = $postRepository;
        $this->slugify = $slugify;
        $this->em = $em;
    }

    /**
     * @Route("/posts", name="blog_posts")
     * @return Response
     */
    public function posts()
    {
        $posts = $this->postRepository->findAll();

        return $this->render('posts/index.html.twig',
            [
                'posts' => $posts
            ]);
    }


    /**
     * @Route("/post/{slug}",name="blog_show")
     * @param Post $post
     * @return Response
     */
    public function post(Post $post)
    {

        return $this->render('posts/show.html.twig',
            [
                'post' => $post
            ]);
    }

    /**
     * @Route("/post_create", name="blog_create")
     * @param Request $request
     * @return Response|RedirectResponse
     * @throws \Exception
     */
    public function addPost(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($this->slugify->slugify($post->getTitle()));
            $post->setCreatedAt(new \DateTime());


            $this->em->persist($post);
            $this->em->flush();

            return $this->redirectToRoute('blog_posts');

        }

        return $this->render('posts/create.html.twig',
            [
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/posts/{slug}/edit", name="blog_post_edit")
     * @param Post $post
     * @param Request $request
     * @param Slugify $slugify
     * @return RedirectResponse|Response
     */
    public function edit(Post $post, Request $request, Slugify $slugify)
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($slugify->slugify($post->getTitle()));

            $this->em->persist($post);
            $this->em->flush();

            return $this->redirectToRoute('blog_posts');
        }

        return $this->render('posts/create.html.twig',
            [
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/posts/{slug}/delete", name="blog_post_delete")
     * @param Post $post
     * @return RedirectResponse
     */
    public function delete(Post $post)
    {
        $this->em->remove($post);
        $this->em->flush();

        return $this->redirectToRoute('blog_posts');
    }

    /**
     * @Route("/posts/search", name="blog_search")
     * @param Request $request
     * @return Response
     */
    public function search(Request $request)
    {
        $query = $request->query->get('q');

        $posts = $this->postRepository->searchByQuery($query);

        return $this->render('posts/query_post.html.twig',
            [
                'posts' => $posts
            ]);
    }
}
