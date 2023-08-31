<?php

namespace App\Controller\Admin;

use App\Entity\TableTeamTournament;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TableTeamTournamentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TableTeamTournament::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
