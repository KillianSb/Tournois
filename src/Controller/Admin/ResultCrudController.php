<?php

namespace App\Controller\Admin;

use App\Entity\Result;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ResultCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Result::class;
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
