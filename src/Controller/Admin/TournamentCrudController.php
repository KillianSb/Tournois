<?php

namespace App\Controller\Admin;

use App\Entity\Tournament;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TournamentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tournament::class;
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
