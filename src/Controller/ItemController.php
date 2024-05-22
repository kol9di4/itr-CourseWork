<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\ItemAttributeBooleanField;
use App\Entity\ItemAttributeDateField;
use App\Entity\ItemAttributeIntegerField;
use App\Entity\ItemAttributeStringField;
use App\Entity\ItemAttributeTextField;
use App\Entity\ItemCollection;
use App\Form\ItemType;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
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
        private ItemCollectionRepository $itemCollectionRepository,
        private ItemRepository $itemRepository,
    ){}

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
            if ($customAttributeValue->getType() === 'Text') {
                $itemAttributeText = new ItemAttributeTextField();
                $itemAttributeText->setCustomItemAttribute($customAttributeValue);
                $item->addItemAttributeTextField($itemAttributeText);
                $this->entityManager->persist($itemAttributeText);
            }
            if ($customAttributeValue->getType() === 'Boolean') {
                $itemAttributeBoolean = new ItemAttributeBooleanField();
                $itemAttributeBoolean->setCustomItemAttribute($customAttributeValue);
                $item->addItemAttributeBooleanField($itemAttributeBoolean);
                $this->entityManager->persist($itemAttributeBoolean);
            }
            if ($customAttributeValue->getType() === 'Date') {
                $itemAttributeDate = new ItemAttributeDateField();
                $itemAttributeDate->setCustomItemAttribute($customAttributeValue);
                $item->addItemAttributeDateField($itemAttributeDate);
                $this->entityManager->persist($itemAttributeDate);
            }
        }
        $form = $this->createForm(ItemType::class, $item);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $item->setItemCollection($itemCollection);
            $item->setDateAdd(new \DateTime());
            $this->entityManager->persist($item);
            $this->entityManager->flush();
        }

        return $this->render('item/form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/collections/{idCollection}/items', name: 'app_items')]
    public function items(Request $request, int $idCollection): Response
    {
        return $this->redirectToRoute('app_collection_view', ['id'=>$idCollection]);
    }

    #[Route('/collections/{idCollection}/items/{idItem}', name: 'app_item')]
    public function view(Request $request, int $idCollection, int $idItem): Response
    {
        $itemCollection = $this->itemCollectionRepository->findOneBy(['id'=>$idCollection]);
        $item = $this->itemRepository->findOneBy(['id' => $idItem]);
        if ($item->getItemCollection() !== $itemCollection) {
            $this->addFlash('error', 'Item not found');
            return $this->redirectToRoute('app_collection_view', ['id'=>$idCollection]);
        }
        return $this->render('item/view.html.twig', [
            'item' => $item,
        ]);
    }

    #[Route('/collections/{idCollection}/items/{idItem}/update', name: 'app_item_update', methods: ['GET','POST'])]
    public function update(Request $request, int $idCollection, int $idItem): Response
    {
        $itemCollection = $this->itemCollectionRepository->findOneBy(['id'=>$idCollection]);
        $item = $this->itemRepository->findOneBy(['id' => $idItem]);
        if ($item->getItemCollection() !== $itemCollection) {
            $this->addFlash('error', 'Item not found');
            return $this->redirectToRoute('app_collection_view', ['id'=>$idCollection]);
        }

        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($item);
            $this->entityManager->flush();
        }

        return $this->render('item/form.html.twig', [
            'action' => 'update',
            'form' => $form->createView(),
        ]);
    }
}
