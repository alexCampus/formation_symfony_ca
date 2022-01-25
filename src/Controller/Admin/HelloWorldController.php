<?php

namespace App\Controller\Admin;

use App\Service\ExportBddExcel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hello", name="hello_world_")
 */
class HelloWorldController extends AbstractController
{
    /**
     * @Route("/", name="list")
     */
    public function index(): Response
    {
        return $this->render('hello_world/index.html.twig', [
            'controller_name' => 'HelloWorldController',
            'items'           => [1, 5, 9, 7, 43, 56, 90]
        ]);
    }

    /**
     * @Route("/export-excel", name="export_excel")
     */
    public function exportExcel(ExportBddExcel $exportBddExcel): Response
    {
        $exportBddExcel->generate();
        $this->addFlash(
            'notice',
            'Excel Généré'
        );

        return $this->redirectToRoute('post_list');
    }

    /**
     * @Route("/{id}", name="show")
     */
    public function show(int $id, Request $request): Response
    {
        return $this->render('hello_world/show.html.twig', [
            'controller_name' => 'HelloWorldController',
            'toto'            => $id
        ]);
    }


}
