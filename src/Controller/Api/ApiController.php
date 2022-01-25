<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\ExportPostXls;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/api", name="api_")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/export-post-excel", name="export_post")
     */
    public function index(ExportPostXls $exportPostXls, Request $request, TokenStorageInterface $tokenStorage): Response
    {
        $token = $tokenStorage->getToken();
        $msg   = "Excel Post Liste créé";
        $user  = null;
        if (!$token) {
            $msg = 'Oupps pas de token';
        } else {
            $user = $token->getUser();
        }


        if (!$user instanceof User) {
            $msg = 'Oups pas de user';
        }

        $exportPostXls->generate($request->query->all(), $user);

        return new JsonResponse(['message' => $msg, 'user' => $user->getFirstName() . " " . $user->getLastName()]);
    }
}
