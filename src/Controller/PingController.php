<?php


namespace App\Controller;
use App\Entity\Ping;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PingController extends AbstractController
{
    public function ping(ManagerRegistry $doctrine): JsonResponse
    {
        $request = Request::createFromGlobals();
        $dateTime = new \DateTime("now");
        //dump($request->getContent());
        error_log($request->getContent(), 0);
        $data = json_decode($request->getContent(), true);

        $entityManager = $doctrine->getManager();

        $ping = new Ping();
        $ping->setCounter($data["counter"]);
        $ping->setService($data["service"]);
        $ping->setDevice($data["device"]);
        $ping->setRegisteredOn($dateTime);

        $entityManager->persist($ping);
        $entityManager->flush();

        return new JsonResponse(['service' => $data["service"], 'counter' => $data["counter"]]);
    }
}