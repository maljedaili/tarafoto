<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PostController extends AbstractController
{
    //    /**
    //  * @Route("/post", name="post")
    //  */
    // public function index()
    // {
    //     $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();

    //     return $this->render('post/index.html.twig', [
    //         'posts' => $posts,
    //     ]);
    // }
    //Create Post

    /**
     * @Route("/create", name="create")
     */
    public function addPost(Request $request, Security $security): Response
    {
        //create a new post
        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);
        $user= $security->getUser();
        $post->setAuthor($user->getId());

        if ($form->isSubmitted() && $form->isValid()) {
            //entity manager
            $entityManger = $this->getDoctrine()->getManager();
            $entityManger->persist($post);
            $entityManger->flush();
        }

        

        return $this->render('post/create.html.twig', [
            'form_title' => 'add title',
            'form_descripstion' => $form->createView(),
        ]);
        
    }

    //READ

    /**
     * @Route("/show", name="show")
     */
    public function posts()
    {
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();

        return $this->render('post/show.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/post/{id}", name="post")
     */
    public function post(int $id): Response
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        return $this->render('post/index.html.twig', [
            'post' => $post,
        ]);
    }

    //Update Post

    /**
     * @Route("/update-post/{id}", name="update_post")
     */
    public function updatePost(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $post = $entityManager->getRepository(Post::class)->find($id);
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
        }

        return $this->render('post/update.html.twig', [
            'form_title' => 'Your title has been update',
            'form_descripstion' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function deletePost(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('show');
    }
}
