<?php


namespace App\Controller;


use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends AbstractController
{
    function summary(ManagerRegistry $doctrine, Request $request): StreamedResponse
    {
        $domain = $request->getHost();
        $dateTime = new \DateTime("now");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
        $entityManager = $doctrine->getManager();

        $qb = $entityManager->createQueryBuilder()
            ->select('a.id, a.agent, a.status, a.attented_on, a.registered_on, p.service, p.counter')
            ->from('App\Entity\Attention', 'a')
            ->leftJoin(
                'App\Entity\Ping',
                'p',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'a.ping = p.id'
            )
            ->where('a.registered_on BETWEEN :dateMin AND :dateMax')
            ->andWhere("p.site = :site")
            ->setParameters(
                [
                    'dateMin' => $dateTime->format('Y-m-d 00:00:00'),
                    'dateMax' => $dateTime->format('Y-m-d 23:59:59'),
                    'site' => $domain
                ]
            );
        $results = $qb->getQuery()->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        //echo "<pre>";
        //var_dump($results);
        //echo "</pre>";
        $sheet->setCellValueByColumnAndRow(1,2,"ID");
        $sheet->setCellValueByColumnAndRow(2,2,"AGENTE");
        $sheet->setCellValueByColumnAndRow(3,2,"SERVICIO");
        $sheet->setCellValueByColumnAndRow(4,2,"ESTADO");
        $sheet->setCellValueByColumnAndRow(5,2,"TICKET");
        $sheet->setCellValueByColumnAndRow(6,2,"FEC. HOR. LLAMADO");
        $sheet->setCellValueByColumnAndRow(7,2,"FEC. HOR. ATENCION");
        for ($i = 0; $i<count($results); $i++ ){

            switch ($results[$i]["service"]){
                case "TV": $service = "ATENDIDO"; break;
                case "SMART SERVICE": $service = "NO ATENDIDO"; break;
                case "MOBILE": $service = "LLAMADO"; break;
                default: $service = ""; break;
            }
            $zeros = str_pad($results[$i]["counter"], 3, "0", STR_PAD_LEFT);
            switch ($results[$i]["service"]){
                case "TV": $ticket = "TV". $zeros; break;
                case "SMART SERVICE": $ticket = "SM". $zeros; break;
                case "MOBILE": $ticket = "MO". $zeros; break;
                default: $ticket = ""; break;
            }

            switch ($results[$i]["status"]){
                case "A": $status = "ATENDIDO"; break;
                case "S": $status = "NO ATENDIDO"; break;
                case "C": $status = "LLAMADO"; break;
                default: $status = ""; break;
            }

            $sheet->setCellValueByColumnAndRow(1,($i+3),$results[$i]["id"]);
            $sheet->setCellValueByColumnAndRow(2,($i+3),$results[$i]["agent"]);
            $sheet->setCellValueByColumnAndRow(3,($i+3),$results[$i]["service"]);
            $sheet->setCellValueByColumnAndRow(4,($i+3),$status);
            $sheet->setCellValueByColumnAndRow(5,($i+3),$ticket);
            $sheet->setCellValueByColumnAndRow(6,($i+3),$results[$i]["registered_on"]);
            $sheet->setCellValueByColumnAndRow(7,($i+3),$results[$i]["attented_on"]);
        }

        $writer = new Xlsx($spreadsheet);
        $response =  new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );
        $time = $dateTime->format('Ymd_His');
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="Reporte'.$time.'.xls"');
        $response->headers->set('Cache-Control','max-age=0');
        return $response;
    }
}