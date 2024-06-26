<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Service\FileUploader;
use App\Entity\ItemCollection;
use App\Entity\Image;
use App\Form\CollectionType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CollectionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ){}

    #[Route('/collections', name: 'app_collections')]
    public function index(Request $request, ItemCollectionRepository $itemCollectionRepository, PaginatorInterface $paginator): Response
    {
        $collections = $itemCollectionRepository->findAll();
        usort($collections, function($c1, $c2){
            return count($c1->getItems()) < count($c2->getItems());
        });
        $collections = $paginator->paginate(
            $collections, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('collection/all-collecions.html.twig', [
            'collections' => $collections,
        ]);
    }

    #[Route('/collections/create', name: 'app_collection_create', methods: ['GET', 'POST'])]
    public function create(Request $request, FileUploader $fileUploader): Response
    {
        $itemCollection = new ItemCollection();
        $image = (new Image())->setPath('no-image.jpg');
        $itemCollection->setImage($image);
        $form = $this->createForm(CollectionType::class, $itemCollection);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $image->setPath($imageFileName);
            }
            $user = $this->getUser();
            $itemCollection->setImage($image);
            $itemCollection->setUser($user);
            $this->entityManager->persist($image);
            $this->entityManager->persist($itemCollection);
            $this->entityManager->flush();
            $this->addFlash('success', 'Collection created.');
            return $this->redirectToRoute('app');
        }

        return $this->render('collection/form.html.twig', [
            'action' => 'create',
            'form'  => $form
        ]);
    }

    #[Route('/collections/{id}', name: 'app_collection_view', requirements: ['id' => '\d+'])]
    public function view(ItemCollection $itemCollection, ItemRepository $itemRepository): Response
    {
        $items = $itemRepository->findByCollectionOrderByDate($itemCollection);
        return $this->render('collection/vew.html.twig', [
            'controller_name' => 'View',
            'collection' => $itemCollection,
            'items' => $items,
        ]);
    }

    #[Route('/collections/{category}', name: 'app_collection_category')]
    public function category(ItemCollectionRepository $itemCollectionRepository,CategoryRepository $categoryRepository ,string $category): Response
    {
        $categoryObj = $categoryRepository->findBy(['name' => $category]);
        $collections = $itemCollectionRepository->findBy(['category' => $categoryObj]);
        return $this->render('collection/category-filter.html.twig', [
            'category' => $category,
            'collections' => $collections,
        ]);
    }

    #[Route('/collections/{id}/delete', name: 'app_collection_delete', requirements: ['id' => '\d+'],methods: ['POST'])]
    public function delete(ItemCollection $itemCollection): Response
    {
        if($this->isHaveRightsForEdit($itemCollection->getUser())) {
            $this->entityManager->remove($itemCollection);
            $this->entityManager->flush();
            $this->addFlash('success', 'Collection deleted.');
            exit();
        }
        $this->addFlash('danger', 'No permissions to delete.');
        exit();
    }
    #[Route('/collections/{id}/update', name: 'app_collection_update', methods: ['GET', 'POST'])]
    public function update(Request $request, FileUploader $fileUploader, ItemCollection $itemCollection): Response
    {
        if(!$this->isHaveRightsForEdit($itemCollection->getUser())) {
            $this->addFlash('danger', 'No permissions to edit.');
            return $this->redirectToRoute('app_collection_view',['id' => $itemCollection->getId()]);
        }

        $originalCustomAttributes = new ArrayCollection();
        foreach ($itemCollection->getCustomItemAttributes() as $customItemAttribute) {
            $originalCustomAttributes->add($customItemAttribute);
        }

        $form = $this->createForm(CollectionType::class, $itemCollection);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $image = new Image();
                $image->setPath($imageFileName);
                $itemCollection->setImage($image);
                $this->entityManager->persist($image);
            }

            $this->entityManager->flush();
            $this->addFlash('success', 'Collection updated.');
            return $this->redirectToRoute('app');
        }

        return $this->render('collection/form.html.twig', [
            'action' => 'update',
            'form'  => $form,
            'itemCollection' => $itemCollection,
        ]);
    }
    private function isHaveRightsForEdit(User $autor){
        $user = $this->getUser();
        $isUserMatched = $user->getId() === $autor->getId();
        $isSetRole = in_array('ROLE_ADMIN', $user->getRoles());
        return ($isUserMatched || $isSetRole);
    }
}

