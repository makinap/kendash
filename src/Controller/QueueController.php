<?php


namespace App\Controller;


use App\Entity\Attention;
use App\Entity\Ping;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class QueueController extends AbstractController
{
    public function queue(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $domain = $request->getHost();
        $dateTime = new \DateTime("now");
        $entityManager = $doctrine->getManager();

        $qb = $entityManager->createQueryBuilder()
            ->select('p.id, p.service, p.counter')
            ->from('App\Entity\Ping', 'p')
            ->leftJoin(
                'App\Entity\Attention',
                'a',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'a.ping = p.id'
            )
            ->where('p.registered_on BETWEEN :dateMin AND :dateMax')
            ->andWhere("a.id IS NULL")
            ->andWhere("p.site = :site")
            ->setParameters(
                [
                    'dateMin' => $dateTime->format('Y-m-d 00:00:00'),
                    'dateMax' => $dateTime->format('Y-m-d 23:59:59'),
                    'site' => $domain
                ]
            );
        return new JsonResponse($qb->getQuery()->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY));
    }

    public function pick_next(ManagerRegistry $doctrine): JsonResponse
    {
        $dateTime = new \DateTime("now");
        $entityManager = $doctrine->getManager();

        $request = Request::createFromGlobals();
        error_log($request->getContent(), 0);
        $data = json_decode($request->getContent(), true);

        $qb_base = $entityManager->createQueryBuilder()
            ->select('p')
            ->from('App\Entity\Ping', 'p')
            ->leftJoin(
                'App\Entity\Attention',
                'a',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'a.ping = p.id'
            )
            ->where('p.registered_on BETWEEN :dateMin AND :dateMax')
            //->andWhere("a.id IS NULL")
            ->orderBy('p.id', 'ASC')
            ->setMaxResults( 1 )
            ->setParameters(
                [
                    'dateMin' => $dateTime->format('Y-m-d 00:00:00'),
                    'dateMax' => $dateTime->format('Y-m-d 23:59:59'),
                ]
            );

        $qb_called = clone $qb_base;
        $qb_called->andWhere("a.status = 'C'");
        $qb_called->andWhere("a.agent = :agent");
        $qb_called->setParameter('agent', $data["agent"]);
        $query_called = $qb_called->getQuery();
        $called_ping = null;


        $called_ping = $query_called->getOneOrNullResult();

        if(!is_null($called_ping)) return new JsonResponse($query_called->getSingleResult(AbstractQuery::HYDRATE_ARRAY));

        $qb_wo_called = clone $qb_base;
        $qb_wo_called->andWhere("a.id IS NULL");
        $query_wo_called = $qb_wo_called->getQuery();
        $ping = $query_wo_called->getOneOrNullResult();

        if(is_null($ping)) return new JsonResponse(null);

        $ary_ping = $query_wo_called->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

        $attention = new Attention();
        $attention->setAgent($data["agent"]);
        $attention->setPing($ping);
        $attention->setStatus("C");
        $attention->setRegisteredOn($dateTime);

        $entityManager->persist($attention);
        $entityManager->flush();

        return new JsonResponse($ary_ping);
    }

    public function attention(ManagerRegistry $doctrine): JsonResponse
    {
        $dateTime = new \DateTime("now");
        $request = Request::createFromGlobals();
        error_log($request->getContent(), 0);

        $data = json_decode($request->getContent(), true);

        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository(Attention::class);
        $ping = $entityManager->find("App\Entity\Ping", $data["ping_id"]);
        $attention = $repository->findOneBy(
            ['ping' => $ping]
        );

        $attention->setStatus($data["new_status"]);
        $attention->setAttentedOn($dateTime);

        $entityManager->persist($attention);
        $entityManager->flush();

        return new JsonResponse([]);
    }

}