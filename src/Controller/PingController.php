<?php


namespace App\Controller;
use App\Entity\Agent;
use App\Entity\Device;
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
        $domain = $request->getHost();
        $dateTime = new \DateTime("now");
        //dump($request->getContent());
        error_log($request->getContent(), 0);

        $data = json_decode($request->getContent(), true);

        $entityManager = $doctrine->getManager();

        $ping = new Ping();
        $ping->setCounter($data["counter"]);
        $ping->setService($data["service"]);
        $ping->setDevice($data["device"]);
        $ping->setSite($domain);
        $ping->setRegisteredOn($dateTime);

        $device = $doctrine->getRepository(Device::class)->findBy(array('serie'=> $data['device']));
        if(!$device){
            $site = $request->getHost();
            $new_device = new Device();
            $new_device->setSerie($data['device']);
            $new_device->setSite($site);
            $entityManager->persist($new_device);
        }

        $entityManager->persist($ping);
        $entityManager->flush();

        return new JsonResponse(['service' => $data["service"], 'counter' => $data["counter"]]);
    }
}