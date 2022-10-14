<?php

namespace App\Controller\Admin;

use App\Entity\Ping;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ping::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $sites = $this->getParameter('availables_sites');
        $options = array();
        foreach ($sites as $site){
            $options[$site["domain"]] = $site["domain"];
        }

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('device', 'Dispositivo'),
            TextField::new('service', 'Tipo.Servicio'),
            IdField::new('counter', '# Ticket'),
            ChoiceField::new('site', 'Dominio')->setChoices($options),
            DateField::new('registered_on', 'Fec.Registrado')
                ->setFormat('dd/MM/yy HH:mm')
                ->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel("Agregar Ticket");
            })
            ->remove(Crud::PAGE_DETAIL, Action::DELETE);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the visible title at the top of the page and the content of the <title> element
            // it can include these placeholders:
            //   %entity_name%, %entity_as_string%,
            //   %entity_id%, %entity_short_id%
            //   %entity_label_singular%, %entity_label_plural%
            ->setPageTitle('index', 'Tickets')
            ->setPageTitle('new', 'Nuevo Ticket')
            ->setPageTitle('edit',
                fn(Ping $ping) => sprintf('Editar <b>%s</b>',
                    $ping->getShortService() . str_pad($ping->getCounter(), "4", "0", STR_PAD_LEFT)));
    }

}
