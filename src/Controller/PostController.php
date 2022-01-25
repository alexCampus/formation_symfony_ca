<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\ExportPostXls;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\ErrorHandler;
use Monolog\Logger;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/post", name="post_")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="list")
     */
    public function index(PostRepository $postRepository, Request $request): Response
    {
//        $data = $postRepository->search($request->query->all());
//        $json = [];
//        foreach ($data as $key => $datum) {
//            $json[$key]['name']    = $datum->getName();
//            $json[$key]['content'] = $datum->getContent();
//        }
//
//        return new JsonResponse($json);

        $posts = $postRepository->search($request->query->all());

        return $this->render('post/index.html.twig', [
            'posts'  => $posts,
            'params' => $request->query->all()
        ]);
    }

    /**
     * @Route("/{id}", name="show", requirements={"id" = "\d+"})
     */
    public function show(Post $post, Request $request, EntityManagerInterface $entityManager)
    {

        $comment = new Comment();
        $form    = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $post->addComment($comment);
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/new", name="new")
     */
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader)
    {

        $post = new Post();
        $form = $this->createForm(PostType::class, $post, ['toto' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $img = $form->get('imageName')->getData();

            if ($img) {
                $newFilename = $fileUploader->upload($img);
                // updates the 'img' property to store the
                // instead of its contents
                $post->setImageName($newFilename);
            }
            $post->setUser($this->getUser());
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
            'post' => $post
        ]);
    }

    /**
     * @Security("is_granted('POST_EDIT', post)", message="Je ne suis pas autorisé")
     * @Route("/edit/{id}", name="edit", requirements={"id" = "\d+"})
     */
    public function edit(Post $post, Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader)
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $img = $form->get('imageName')->getData();

            if ($img) {
                $newFilename = $fileUploader->upload($img);
                // updates the 'img' property to store the
                // instead of its contents
                $post->setImageName($newFilename);
            }
            $post->setUser($this->getUser());
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     *
     * @Route("/delete/{id}", name="delete", requirements={"id" = "\d+"})
     */
    public function delete(Post $post, Request $request, EntityManagerInterface $entityManager)
    {
        if ($this->isGranted('POST_DELETE', $post)) {
            if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
                $entityManager->remove($post);
                $entityManager->flush();
            }
        }


        return $this->redirectToRoute('post_list');
    }

    /**
     * @Route("/export", name="export")
     * @return RedirectResponse
     */
    public function exportExcel(Request $request, ExportPostXls $exportExcel)
    {
        $exportExcel->generate($request->query->all());

        return $this->redirectToRoute('post_list', $request->query->all());
    }
}
