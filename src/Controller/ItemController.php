<?php

namespace App\Controller;

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
use App\Entity\Tag;
use App\Entity\User;
use App\Enum\CustomAttributeEnum;
use App\Enum\LikeTypesEnum;
use App\Form\CommentType;
use App\Form\ItemType;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\LikeRepository;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ItemController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ItemCollectionRepository $itemCollectionRepository,
        private ItemRepository $itemRepository,
        private LikeRepository $likeRepository,
    ){}

    #[Route('/collections/{id}/items/create', name: 'app_item_create', methods: ['GET','POST'])]
    public function index(Request $request,ItemCollection $itemCollection,ValidatorInterface $validator, TagRepository $tagRepository): Response
    {
        $item = $this->setAttributesToAnItem($itemCollection);

        $tagsForItem = '';
        foreach ($item->getTags() as $tag)
        {
            $tagsForItem .= $tag->getName() . ', ';
        }

        $allTags = $tagRepository->findAll();
        $whitelistTags = '';
        foreach ($allTags as $tag) {
            $whitelistTags .= $tag->getName() . ', ';
        }
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tagsRequest = json_decode($request->request->get('tags'));
            $tagsForItem = $this->checkTags($tagsRequest, $tagRepository);
            foreach ($tagsForItem as $tag) {
                $item->addTag($tag);
            }
            $item->setItemCollection($itemCollection);
            $this->entityManager->persist($item);
            $this->entityManager->flush();
            $this->addFlash('success', 'Item created.');
            return $this->redirectToRoute('app_collection_view', ['id'=>$itemCollection->getId()]);
        }
        return $this->render('item/form.html.twig', [
            'action' => 'create',
            'form' => $form,
            'itemCollection' => $itemCollection,
            'errors' => '',
            'tagsForItem' => $tagsForItem,
            'whitelistTags' => $whitelistTags,
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
        $likesInfo = $this->getLikesInfo($item);

        if ($request->isMethod('POST') && $commentForm->handleRequest($request)->isValid()) {
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_item',['idItem'=>$idItem,'idCollection'=>$idCollection]);
        }

        $this->entityManager->flush();
        return $this->render('item/view.html.twig', array_merge([
            'item' => $item,
            'commentForm' => $commentForm,
        ],$likesInfo)
        );
    }

    #[Route('/collections/{idCollection}/items/{idItem}/like', name: 'app_item_like', methods: ['POST'])]
    public function like(Request $request, int $idCollection, int $idItem): void
    {
        $user = $this->getUser();
        $likeType = (int)$request->request->get('likeType')===1
            ? LikeTypesEnum::Like
            : LikeTypesEnum::Dislike;
        $item = $this->itemRepository->findOneBy(['id' => $idItem]);
        $this->setLike($item, $user, $likeType->value);
        $this->entityManager->flush();
        $likesInfo = $this->getLikesInfo($item);
        $likeDiv = $this->render('item/like/index.html.twig',
            array_merge(['item' => $item], $likesInfo)
        );
        echo  json_encode($likeDiv->getContent());
        exit();
    }

    #[Route('/collections/{idCollection}/items/{idItem}/update', name: 'app_item_update', methods: ['GET','POST'])]
    public function update(Request $request, int $idCollection, int $idItem, TagRepository $tagRepository): Response
    {

        $item = $this->itemRepository->findOneBy(['id'=>$idItem]);
        $itemCollection = $this->itemCollectionRepository->findOneBy(['id'=>$idCollection]);
        if(!$this->isHaveRightsForEdit($itemCollection->getUser())) {
            $this->addFlash('danger', 'No permissions to edit.');
            return $this->redirectToRoute('app_collection_view',['id' => $itemCollection->getId()]);
        }

        $tagsForItem = '';
        foreach ($item->getTags() as $tag)
        {
            $tagsForItem .= $tag->getName() . ', ';
        }

        $allTags = $tagRepository->findAll();
        $whitelistTags = '';
        foreach ($allTags as $tag) {
            $whitelistTags .= $tag->getName() . ', ';
        }
        $form = $this->createForm(ItemType::class, $item);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tagsRequest = json_decode($request->request->get('tags'));
            if(!empty($tagsRequest))
            {
                foreach ($item->getTags() as $tag) {
                    $item->removeTag($tag);
                }
                $tagsForItem = $this->checkTags($tagsRequest, $tagRepository);
                foreach ($tagsForItem as $tag) {
                    $item->addTag($tag);
                }
                $this->entityManager->persist($item);
                $this->entityManager->flush();
                $this->addFlash('success', 'Update successful');
                return $this->redirectToRoute('app_item', [
                    'idCollection'=>$itemCollection->getId(),
                    'idItem'=>$item->getId(),
                ]);
            }

            $form->addError(new FormError('Invalid tags.'));
        }

        return $this->render('item/form.html.twig', [
            'action' => 'update',
            'form' => $form,
            'item' => $item,
            'tagsForItem' => $tagsForItem,
            'whitelistTags' => $whitelistTags,
        ]);
    }

    #[Route('/collections/{idCollection}/items/{idItem}/delete', name: 'app_item_delete', methods: ['POST'])]
    public function delete(Request $request, int $idCollection, int $idItem): Response
    {
        $itemCollection = $this->itemCollectionRepository->findOneBy(['id'=>$idCollection]);
        $item = $this->itemRepository->findOneBy(['id'=>$idItem]);
        if($this->isHaveRightsForEdit($itemCollection->getUser())) {
            $this->entityManager->remove($item);
            $this->entityManager->flush();
            $this->addFlash('success', 'Item deleted.');
            exit();
        }
        $this->addFlash('danger', 'No permissions to delete.');
        exit();
    }

    private function checkTags(array $tagsRequest, TagRepository $tagRepository): array{
        $tags = [];
        foreach ($tagsRequest as $tagName)
        {
            $tag = $tagRepository->findOneBy(['name'=>$tagName->value]);
            if(!isset($tag)){
                $tag = new Tag();
                $tag->setName($tagName->value);
            }
            $tags[] = $tag;
            $this->entityManager->persist($tag);
        }
        return $tags;
    }

    private function isHaveRightsForEdit(User $autor){
        $user = $this->getUser();
        $isUserMatched = $user->getId() === $autor->getId();
        $isSetRole = in_array('ROLE_ADMIN', $user->getRoles());
        return ($isUserMatched || $isSetRole);
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
        $result['likeCount'] = $this->getLikeCountByType($item,LikeTypesEnum::Like);
        $result['dislikeCount'] = $this->getLikeCountByType($item,LikeTypesEnum::Dislike);
        $result['likeType'] =$this->getLikeTypeForItem($item);

        return $result;
    }
    private function getLikeCountByType(Item $item, LikeTypesEnum $likeType): int{
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
