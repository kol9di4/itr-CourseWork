<?php

namespace App\Controller;

use App\Repository\ItemCollectionRepository;
use App\Service\FileUploader;
use App\Entity\ItemCollection;
use App\Entity\Image;
use App\Form\CollectionType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CollectionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ){}

    #[Route('/collections', name: 'app_collection')]
    public function index(ItemCollectionRepository $itemCollectionRepository): Response
    {
        $collections = $itemCollectionRepository->findAll();
        return $this->render('collection/index.html.twig', [
            'controller_name' => 'CollectionController',
            'collections' => $collections,
        ]);
    }



    #[Route('/collections/create', name: 'app_collection_create', methods: ['GET', 'POST'])]
    public function create(Request $request, FileUploader $fileUploader): Response
    {
        $itemCollection = new ItemCollection();
        $form = $this->createForm(CollectionType::class, $itemCollection);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $image = new Image();
                $image->setPath($imageFileName);
                $user = $this->getUser();
                $itemCollection->setImage($image);
                $itemCollection->setUser($user);
                $this->entityManager->persist($image);
                $this->entityManager->persist($itemCollection);
                $this->entityManager->flush();
                $this->addFlash('success', 'Collection created.');
                return $this->redirectToRoute('app_collection');
            }
            $form->get('image')->addError(new FormError('Select a picture to upload!'));
        }

        return $this->render('collection/form.html.twig', [
            'action' => 'create',
            'form'  => $form
        ]);
    }
    #[Route('/collections/{id}', name: 'app_collection_view')]
    public function view(ItemCollection $itemCollection): Response
    {
        return $this->render('collection/vew.html.twig', [
            'controller_name' => 'View',
            'itemCollection' => $itemCollection,
        ]);
    }

    #[Route('/collections/{id}/update', name: 'app_collection_update', methods: ['GET', 'POST'])]
    public function update(Request $request, FileUploader $fileUploader, ItemCollection $itemCollection): Response
    {
        $user = $this->getUser();
        $isUserMatched = $user->getId() === $itemCollection->getUser()->getId();
        $isSetRole = in_array('ROLE_ADMIN', $user->getRoles());
        if(!($isUserMatched || $isSetRole)) {
            $this->addFlash('error', 'No permissions to edit.');
            return $this->redirectToRoute('app_collection_view',['id' => $itemCollection->getId()]);
        }

        $originalCustomAttributes = new ArrayCollection();
        foreach ($itemCollection->getCustomItemAttributes() as $customItemAttribute) {
            $originalCustomAttributes->add($customItemAttribute);
        }

        $form = $this->createForm(CollectionType::class, $itemCollection);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
//            foreach ($originalCustomAttributes as $customItemAttribute) {
//                if (false === $itemCollection->getCustomItemAttributes()->contains($customItemAttribute)) {
//                    dd($customItemAttribute);
//                    $customItemAttribute->getItemCollection()->removeElement($itemCollection);
//                    $this->entityManager->persist($customItemAttribute);
//                }
//            }
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
            return $this->redirectToRoute('app_collection',['id' => $itemCollection->getId()]);
        }

        return $this->render('collection/form.html.twig', [
            'action' => 'update',
            'form'  => $form,
            'itemCollection' => $itemCollection,
        ]);
    }
}

