<?php

namespace App\Controller\Admin;

use App\Entity\Agent;
use App\Entity\Attention;
use App\Entity\Device;
use App\Entity\Ping;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Locale;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatableMessage;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin/index.html.twig');

    }

    public function configureDashboard(): Dashboard
    {
        TextField::new('Device', new TranslatableMessage('Dispositivo'));
        return Dashboard::new()
            ->setTitle('NEOPROJECTS')
            ->generateRelativeUrls()
            ->setTranslationDomain('admin')
            ->setLocales([
                'es', // locale without custom options
            ]);
    }

    public function configureMenuItems(): iterable
    {
        //yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
        return [

            MenuItem::linkToCrud('Tickets', 'fa fa-tags', Ping::class),
            MenuItem::linkToCrud('Atencion', 'fa fa-file-text', Attention::class),
            MenuItem::linkToCrud('Agentes', 'fa fa-person', Agent::class),
            MenuItem::linkToCrud('Dispositivos', 'fa fa-tablet', Device::class)

            // ...
        ];
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userMenu = parent::configureUserMenu($user);
        $userMenu->setMenuItems([]);
        return $userMenu;
    }

}
