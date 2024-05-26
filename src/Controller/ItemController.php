<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Item;
use App\Entity\ItemAttributeBooleanField;
use App\Entity\ItemAttributeDateField;
use App\Entity\ItemAttributeIntegerField;
use App\Entity\ItemAttributeStringField;
use App\Entity\ItemAttributeTextField;
use App\Entity\ItemCollection;
use App\Entity\Like;
use App\Enum\CustomAttributeEnum;
use App\Form\CommentType;
use App\Form\ItemType;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ItemController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ItemCollectionRepository $itemCollectionRepository,
        private ItemRepository $itemRepository,
        private LikeRepository $likeRepository,
    ){}

    #[Route('/collections/{id}/items/create', name: 'app_item_create', methods: ['GET','POST'])]
    public function index(Request $request,ItemCollection $itemCollection): Response
    {
        $item = new Item();
        $customAttributes = $itemCollection->getCustomItemAttributes()->getValues();
        foreach ($customAttributes as $customAttributeValue) {
            if ($customAttributeValue->getType() === CustomAttributeEnum::Integer) {
                $itemAttributeInteger = new ItemAttributeIntegerField();
                $itemAttributeInteger->setCustomItemAttribute($customAttributeValue);
                $item->addItemAttributeIntegerField($itemAttributeInteger);
                $this->entityManager->persist($itemAttributeInteger);
            }
            if ($customAttributeValue->getType() === CustomAttributeEnum::String) {
                $itemAttributeString = new ItemAttributeStringField();
                $itemAttributeString->setCustomItemAttribute($customAttributeValue);
                $item->addItemAttributeStringField($itemAttributeString);
                $this->entityManager->persist($itemAttributeString);
            }
            if ($customAttributeValue->getType() === CustomAttributeEnum::Text) {
                $itemAttributeText = new ItemAttributeTextField();
                $itemAttributeText->setCustomItemAttribute($customAttributeValue);
                $item->addItemAttributeTextField($itemAttributeText);
                $this->entityManager->persist($itemAttributeText);
            }
            if ($customAttributeValue->getType() === CustomAttributeEnum::Boolean) {
                $itemAttributeBoolean = new ItemAttributeBooleanField();
                $itemAttributeBoolean->setCustomItemAttribute($customAttributeValue);
                $item->addItemAttributeBooleanField($itemAttributeBoolean);
                $this->entityManager->persist($itemAttributeBoolean);
            }
            if ($customAttributeValue->getType() === CustomAttributeEnum::Date) {
                $itemAttributeDate = new ItemAttributeDateField();
                $itemAttributeDate->setCustomItemAttribute($customAttributeValue);
                $itemAttributeDate->setValue(new \DateTime());
                $item->addItemAttributeDateField($itemAttributeDate);
                $this->entityManager->persist($itemAttributeDate);
            }
        }
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $item->setItemCollection($itemCollection);
            $this->entityManager->persist($item);
            $this->entityManager->flush();
        }

        return $this->render('item/form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),
            'itemCollection' => $itemCollection,
        ]);
    }

    #[Route('/collections/{idCollection}/items', name: 'app_items')]
    public function items(Request $request, int $idCollection): Response
    {
        return $this->redirectToRoute('app_collection_view', ['id'=>$idCollection]);
    }

    #[Route('/collections/{idCollection}/items/{idItem}', name: 'app_item', methods: ['GET', 'POST'])]
    public function view(Request $request, int $idCollection, int $idItem): Response
    {
        if ($this->isItemPartCollection($idCollection, $idItem)) {
            $this->addFlash('error', 'Item not found');
            return $this->redirectToRoute('app_collection_view', ['id'=>$idCollection]);
        }
        $item = $this->itemRepository->findOneBy(['id' => $idItem]);
        $item->setViews($item->getViews() + 1);
        $comment = new Comment();
        $comment->setItem($item);
        $comment->setUser($this->getUser());
        $commentForm  = $this->createForm(CommentType::class,$comment);
        if ($request->isMethod('POST') && $commentForm->handleRequest($request)->isValid()) {
            $this->entityManager->persist($comment);
        }
        $likeCount = $this->likeRepository->count(['item'=>$item,'type'=>1]);
        $dislikeCount = $this->likeRepository->count(['item'=>$item, 'type'=>-1]);
        $this->entityManager->flush();
        return $this->render('item/view.html.twig', [
            'item' => $item,
            'commentForm' => $commentForm->createView(),
            'likeCount' => $likeCount,
            'dislikeCount' => $dislikeCount,
        ]);
    }

    #[Route('/collections/{idCollection}/items/{idItem}/like', name: 'app_item_like', methods: ['POST'])]
    public function like(Request $request, int $idCollection, int $idItem): void
    {
//        if ($this->isItemPartCollection($idCollection, $idItem)) {
//            $this->addFlash('error', 'Item not found');
//            return $this->redirectToRoute('app_item', ['idCollection'=>$idCollection, 'idItem'=>$idItem]);
//        }
        $user = $this->getUser();
        $item = $this->itemRepository->findOneBy(['id' => $idItem]);
        $like = $this->likeRepository->findOneBy(['user'=>$user,'item'=>$item]);
        $likeTypeRequest = $request->request->get('likeType');
        if (empty($like))
        {
            $newLike = new Like();
            $newLike->setUser($user);
            $newLike->setItem($item);
            $newLike->setType($likeTypeRequest);
            $this->entityManager->persist($newLike);
        }
        else
        {
            dump($like->getType());
            dump((int)$likeTypeRequest);
            if ($like->getType() === $likeTypeRequest*-1 || $like->getType() === 0)
                $like->setType($likeTypeRequest);
            elseif ($like->getType() === (int)$likeTypeRequest)
                $like->setType(0);

        }
        $this->entityManager->flush();
        echo  '1';
        exit();
//        return $this->redirectToRoute('app_item', ['idCollection'=>$idCollection, 'idItem'=>$idItem]);
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
            'item' => $item,
        ]);
    }
    private function isItemPartCollection(int $idCollection, int $idItem): bool{
        $itemCollection = $this->itemCollectionRepository->findOneBy(['id' => $idCollection]);
        $item = $this->itemRepository->findOneBy(['id' => $idItem]);
        return ($item->getItemCollection() !== $itemCollection);
    }
}
