<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Cocur\Slugify\SlugifyInterface;
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
     * PostsController constructor.
     * @param PostRepository $postRepository
     * @param SlugifyInterface $slugify
     */
    public function __construct(PostRepository $postRepository, SlugifyInterface $slugify)
    {

        $this->postRepository = $postRepository;
        $this->slugify = $slugify;
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
     * @throws \Exception
     * @return Response|RedirectResponse
     */
    public function addPost(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($this->slugify->slugify($post->getTitle()));
            $post->setCreatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('blog_posts');

        }

        return $this->render('posts/create.html.twig',
            [
               'form' => $form->createView()
            ]);

    }

}
