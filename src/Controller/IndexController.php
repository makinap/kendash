<?php


namespace App\Controller;


use App\Entity\Agent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $domain = $request->getHost();

        $agents = $doctrine->getRepository(Agent::class)->findBy(array('site'=> $domain));
        return $this->render('index/index.html.twig',[
            'agents' => $agents,
            'domain' => ""
        ]);
    }
}