<?php

namespace App\Controller\Admin;

use App\Entity\Agent;
use App\Entity\Attention;
use App\Entity\Ping;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\HttpFoundation\Request;
use App\Kernel;

class AttentionCrudController extends AbstractCrudController
{
    private $entityManager;

    public static function getEntityFqcn(): string
    {
        return Attention::class;
    }
    public function __construct(EntityManagerInterface $entityManager, Kernel $kernel)
    {
        $this->entityManager = $entityManager;
        $this->container = $kernel->getContainer();
    }

    public function configureFields(string $pageName): iterable
    {

        $site = $this->container->get('router')->getContext()->getHost();
        $agents_fields = TextField::new('agent',"Agente");
        if (Crud::PAGE_NEW === $pageName) {
            $agents = $this->entityManager->getRepository(Agent::class)->findBy(array('site'=> $site));
            $options = array();
            foreach ($agents as $agent){
                $options[$agent->getCodename()] = $agent->getId();
            }
            $agents_fields = ChoiceField::new('agent',"Agente")->setChoices($options);
        }

        $sites_options = array();
        $sites = $this->getParameter('availables_sites');

        foreach ($sites as $site0){
            $sites_options[$site0["domain"]] = $site0["domain"];
        }
        $sites_fields = ChoiceField::new('ping.site',"Sitio")
            ->setChoices($sites_options)
            ->hideOnForm();

        return [
            IdField::new('id')->hideOnForm(),
            //TextField::new('Ping.service', "Servicio"),
            AssociationField::new('ping', "Ticket")
                ->setCrudController(PingCrudController::class),
            $sites_fields,
            //TextField::new('Ping.device', "Dispositivo"),
            //TextField::new('agent',"Agente"),
            //ChoiceField::new('agent',"Agente")->setChoices($options),
            $agents_fields,
            //TextField::new('status',"Estado"),
            ChoiceField::new('status',"Estado")->setChoices([
                'No atendido' => 'S',
                'Atendido' => 'A',
                'En espera' => 'C',
            ]),
            DateField::new('registered_on',"Fec.Registrado")
                ->setFormat('dd/MM/yy HH:mm')->hideOnForm(),
            DateField::new('attented_on',"Fec.Atendido")
                ->setFormat('dd/MM/yy HH:mm')->hideOnForm(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the visible title at the top of the page and the content of the <title> element
            // it can include these placeholders:
            //   %entity_name%, %entity_as_string%,
            //   %entity_id%, %entity_short_id%
            //   %entity_label_singular%, %entity_label_plural%
            ->setPageTitle('index', 'Atenciones')
            ->setPageTitle('new', 'Nueva Atencion')
            ->setPageTitle('edit',
                fn(Attention $attention) => sprintf('Editar Atencion de <b>%s</b>',
                    $attention->getPing()->getShortService() . str_pad($attention->getPing()->getCounter(), "4", "0", STR_PAD_LEFT)));
    }

    /*public function configureFilters(Filters $filters): Filters
    {

        $sites_options = array();
        $sites = $this->getParameter('availables_sites');

        foreach ($sites as $site0){
            $sites_options[$site0["domain"]] = $site0["domain"];
        }


        return $filters

            //->add(BooleanFilter::new('published'))
            //->add(Filter::new('country')->mapped(false))
            ->add(ChoiceFilter::new('status')->setChoices([
                'No atendido' => 'S',
                'Atendido' => 'A',
                'En espera' => 'C',
            ]))
            ->add(ChoiceFilter::new('ping.site')->setChoices($sites_options))
            ;
    }*/

}
