<?php

namespace App\Controller\Admin;

use App\Entity\Agent;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AgentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Agent::class;
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
            TextField::new('codename', 'Identificador'),
            //TextField::new('site', 'Dominio'),
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
                return $action->setLabel("Agregar Agente");
            })
            ->remove(Crud::PAGE_DETAIL, Action::DELETE);
    }
}
