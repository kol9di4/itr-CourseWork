<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Form\ItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ItemController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
    ){}
    #[Route('/collections/{id}/items/create', name: 'app_item', methods: ['GET','POST'])]
    public function index(Request $request,ItemCollection $itemCollection): Response
    {

        $form = $this->createForm(ItemType::class);
        $customAttributes = $itemCollection->getCustomItemAttributes()->getValues();
        foreach ($customAttributes as $customAttributeValue) {
            $id = $customAttributeValue->getId();
            if ($customAttributeValue->getType() === 'String') {
                $form
                    ->add($id, TextType::class, [
                        'label' => $customAttributeValue->getName(),
                    ]);
            }
            if ($customAttributeValue->getType() === 'Integer') {
                $form
                    ->add($id, IntegerType::class, [
                        'label' => $customAttributeValue->getName(),
                        'row_attr' => ['data-id' => $id, 'data-type'=> $customAttributeValue->getType()],
                    ]);
            }
        }
        if($request->isMethod('POST')) {
            dd($request->request->all()['item']);
        }

//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//        }
//        if ($form->isSubmitted() && $form->isValid()) {
//            $item = new Item();
//            $item->setItemCollection($itemCollection);
//            $item->setDateAdd(new \DateTime());
//            $this->entityManager->persist($item);
//            $this->entityManager->flush();
//        }

        return $this->render('item/index.html.twig', [
            'controller_name' => 'ItemController',
            'form' => $form->createView(),
        ]);
    }
}
