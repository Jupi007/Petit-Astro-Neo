<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Entity\Definition;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefinitionController extends AbstractController
{
    public function index(Definition $definition): Response
    {
        return $this->render('definition/definition.html.twig', [
            'definition' => $definition,
        ]);
    }
}
