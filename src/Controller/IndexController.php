<?php


namespace App\Controller;


use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    public function index(ManagerRegistry $doctrine): Response
    {
        return $this->render('index/index.html.twig');
    }
}