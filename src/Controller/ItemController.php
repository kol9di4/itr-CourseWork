<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\ItemAttributeIntegerField;
use App\Entity\ItemAttributeStringField;
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
    #[Route('/collections/{id}/items', name: 'app_item')]
    public function items(Request $request,ItemCollection $itemCollection): Response
    {
        $items = $itemCollection->getItems();
        return $this->render('item/index.html.twig', [
            'controller_name' => 'ItemController',
            'items' => $items,
        ]);
    }

    #[Route('/collections/{id}/items/create', name: 'app_item_create', methods: ['GET','POST'])]
    public function index(Request $request,ItemCollection $itemCollection): Response
    {
        $item = new Item();
        $item->setName('qwe');
        $customAttributes = $itemCollection->getCustomItemAttributes()->getValues();
        foreach ($customAttributes as $customAttributeValue) {
            if ($customAttributeValue->getType() === 'Integer') {
                $itemAttributeInteger = new ItemAttributeIntegerField();
                $itemAttributeInteger->setCustomItemAttribute($customAttributeValue);
                $item->addItemAttributeIntegerField($itemAttributeInteger);
                $this->entityManager->persist($itemAttributeInteger);
            }
            if ($customAttributeValue->getType() === 'String') {
                $itemAttributeString = new ItemAttributeStringField();
                $itemAttributeString->setCustomItemAttribute($customAttributeValue);
                $item->addItemAttributeStringField($itemAttributeString);
                $this->entityManager->persist($itemAttributeString);
            }
        }
        $form = $this->createForm(ItemType::class, $item);

//        foreach ($customAttributes as $customAttributeValue) {
//            $id = $customAttributeValue->getId();
//            if ($customAttributeValue->getType() === 'String') {
//                $form
//                    ->add(''.$id, TextType::class, [
//                        'label' => $customAttributeValue->getName(),
//                        'row_attr' => ['data-id' => $id, 'data-type'=> $customAttributeValue->getType()],
//                    ]);
//            }
//            if ($customAttributeValue->getType() === 'Integer') {
//                $form
//                    ->add(''.$id, IntegerType::class, [
//                        'label' => $customAttributeValue->getName(),
//                        'row_attr' => ['data-id' => $id, 'data-type'=> $customAttributeValue->getType()],
//                    ]);
//            }
//        }
//        if($request->isMethod('POST')) {
//            dd($request->request->all()['item']);
//        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $item->setItemCollection($itemCollection);
            $item->setDateAdd(new \DateTime());
            $this->entityManager->persist($item);
            $this->entityManager->flush();
        }

        return $this->render('item/form.html.twig', [
            'controller_name' => 'ItemController',
            'form' => $form->createView(),
        ]);
    }
}
