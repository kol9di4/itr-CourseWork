<?php

namespace App\Controller;

use App\Entity\ItemCollection;
use App\Form\ItemType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ItemController extends AbstractController
{
    #[Route('/collections/{id}/items/create', name: 'app_item')]
    public function index(ItemCollection $itemCollection): Response
    {
        $form = $this->createForm(ItemType::class);
        $customAttributes = $itemCollection->getCustomItemAttributes();
        dd($customAttributes->getValues());
        return $this->render('item/index.html.twig', [
            'controller_name' => 'ItemController',
        ]);
    }
}
