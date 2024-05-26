<?php

namespace App\Controller;

use App\Const\LikeTypeConst;
use App\Entity\Comment;
use App\Entity\CustomItemAttribute;
use App\Entity\Item;
use App\Entity\ItemAttributeBooleanField;
use App\Entity\ItemAttributeDateField;
use App\Entity\ItemAttributeIntegerField;
use App\Entity\ItemAttributeStringField;
use App\Entity\ItemAttributeTextField;
use App\Entity\ItemCollection;
use App\Entity\Like;
use App\Entity\User;
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
use Symfony\Component\Security\Core\User\UserInterface;

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
        $item = $this->setAttributesToAnItem($itemCollection);
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $item->setItemCollection($itemCollection);
            $this->entityManager->persist($item);
            $this->entityManager->flush();
            $this->addFlash('success', 'Item created.');
            return $this->redirectToRoute('app_collection_view', ['id'=>$itemCollection->getId()]);
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

        $this->entityManager->flush();
        $likesInfo = $this->getLikesInfo($item);
        return $this->render('item/view.html.twig', array_merge([
            'item' => $item,
            'commentForm' => $commentForm->createView(),
        ],$likesInfo)
        );
    }

    #[Route('/collections/{idCollection}/items/{idItem}/like', name: 'app_item_like', methods: ['POST'])]
    public function like(Request $request, int $idCollection, int $idItem): void
    {
        $user = $this->getUser();
        $likeType = (int)$request->request->get('likeType')===1
            ? LikeTypeConst::LIKE
            : LikeTypeConst::DISLIKE;
        $item = $this->itemRepository->findOneBy(['id' => $idItem]);
        $this->setLike($item, $user, $likeType);
        $this->entityManager->flush();
        $likesInfo = $this->getLikesInfo($item);
        $likeDiv = $this->render('item/like/index.html.twig',
            array_merge(['item' => $item], $likesInfo)
        );
        echo  json_encode($likeDiv->getContent());
        exit();
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
            $this->addFlash('success', 'Update successful');
            return $this->redirectToRoute('app_item', [
                'idCollection'=>$itemCollection->getId(),
                'idItem'=>$item->getId(),
            ]);
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
    private function setAttributesToAnItem(ItemCollection $itemCollection): Item{
        $item = new Item();
        $customAttributes = $itemCollection->getCustomItemAttributes()->getValues();
        foreach ($customAttributes as $customAttributeValue) {
            switch ($customAttributeValue->getType())
            {
                case CustomAttributeEnum::Integer:
                    $item = $this->setIntegerAttributes($item, $customAttributeValue);
                    break;
                case CustomAttributeEnum::String:
                    $item = $this->setStringAttributes($item, $customAttributeValue);
                    break;
                case CustomAttributeEnum::Text:
                    $item = $this->setTextAttributes($item, $customAttributeValue);
                    break;
                case CustomAttributeEnum::Boolean:
                    $item = $this->setBooleanAttributes($item, $customAttributeValue);
                    break;
                case CustomAttributeEnum::Date:
                    $item = $this->setDateAttributes($item, $customAttributeValue);
                    break;
            }
        }
        return $item;
    }
    private function setLike(Item $item, User $user, int $likeType):void
    {
        $like = $this->likeRepository->findOneBy(['user'=>$user,'item'=>$item]);
        if (empty($like))
        {
            $newLike = new Like();
            $newLike->setUser($user);
            $newLike->setItem($item);
            $newLike->setType($likeType);
            $this->entityManager->persist($newLike);
        }
        else
        {
            if ($like->getType() === $likeType*-1 || $like->getType() === 0)
                $like->setType($likeType);
            elseif ($like->getType() === $likeType)
                $like->setType(0);
        }
    }
    private function getLikesInfo(Item $item): array
    {
        $result = [];
        $result['likeCount'] = $this->getLikeCountByType($item,LikeTypeConst::LIKE);
        $result['dislikeCount'] = $this->getLikeCountByType($item,LikeTypeConst::DISLIKE);
        $result['likeType'] =$this->getLikeTypeForItem($item);

        return $result;
    }
    private function getLikeCountByType(Item $item, int $likeType): int{
        return $this->likeRepository->count(['item'=>$item,'type'=>$likeType]);
    }
    private function getLikeTypeForItem(Item $item): int{
        return empty($this->likeRepository->findOneBy(['item'=>$item,'user'=>$this->getUser()]))
            ?0
            :$this->likeRepository->findOneBy(['item'=>$item,'user'=>$this->getUser()])->getType();
    }
    private function setIntegerAttributes(Item $item, CustomItemAttribute $customAttributeValue): Item{
        $itemAttributeInteger = new ItemAttributeIntegerField();
        $itemAttributeInteger->setCustomItemAttribute($customAttributeValue);
        $item->addItemAttributeIntegerField($itemAttributeInteger);
        $this->entityManager->persist($itemAttributeInteger);

        return $item;
    }
    private function setStringAttributes(Item $item, CustomItemAttribute $customAttributeValue): Item{
        $itemAttributeString = new ItemAttributeStringField();
        $itemAttributeString->setCustomItemAttribute($customAttributeValue);
        $item->addItemAttributeStringField($itemAttributeString);
        $this->entityManager->persist($itemAttributeString);

        return $item;
    }
    private function setTextAttributes(Item $item, CustomItemAttribute $customAttributeValue): Item{
        $itemAttributeText = new ItemAttributeTextField();
        $itemAttributeText->setCustomItemAttribute($customAttributeValue);
        $item->addItemAttributeTextField($itemAttributeText);
        $this->entityManager->persist($itemAttributeText);

        return $item;
    }
    private function setBooleanAttributes(Item $item, CustomItemAttribute $customAttributeValue): Item{
        $itemAttributeBoolean = new ItemAttributeBooleanField();
        $itemAttributeBoolean->setCustomItemAttribute($customAttributeValue);
        $item->addItemAttributeBooleanField($itemAttributeBoolean);
        $this->entityManager->persist($itemAttributeBoolean);

        return $item;
    }
    private function setDateAttributes(Item $item, CustomItemAttribute $customAttributeValue): Item{
        $itemAttributeDate = new ItemAttributeDateField();
        $itemAttributeDate->setCustomItemAttribute($customAttributeValue);
        $itemAttributeDate->setValue(new \DateTime());
        $item->addItemAttributeDateField($itemAttributeDate);
        $this->entityManager->persist($itemAttributeDate);

        return $item;
    }
}
