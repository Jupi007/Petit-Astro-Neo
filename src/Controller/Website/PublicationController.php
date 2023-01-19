<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Entity\Publication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PublicationController extends AbstractController
{
    // Controlled by App\Routing\PublicationRouteDefaultsProvider
    public function index(Publication $publication): Response
    {
        return $this->render('publication/publication.html.twig', [
            'publication' => $publication,
        ]);
    }
}
