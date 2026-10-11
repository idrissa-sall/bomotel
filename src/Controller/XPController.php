<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class XPController extends AbstractController
{
    #[Route('/experiences', name: 'app_xp')]
    public function index(): Response
    {
        return $this->render('xp/index.html.twig', [
            'controller_name' => 'XPController',
        ]);
    }
}
