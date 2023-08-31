<?php

namespace App\Controller\Admin;

use App\Entity\VideoGame;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class VideoGameCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return VideoGame::class;
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
