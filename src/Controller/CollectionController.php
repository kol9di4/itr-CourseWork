<?php

namespace App\Controller;

use App\Repository\ItemCollectionRepository;
use App\Service\FileUploader;
use App\Entity\ItemCollection;
use App\Entity\Image;
use App\Form\CollectionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $itemCollection->setDateAdd(new \DateTime());
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
                $this->entityManager->persist($itemCollection);
                $this->entityManager->flush();
                $this->addFlash('success', 'Collection created.');
            }
        }

        return $this->render('collection/form.html.twig', [
            'action' => 'create',
            'form'  => $form
        ]);
    }

    #[Route('/collections/{id}/update', name: 'app_collection_update', methods: ['GET', 'POST'])]
    public function update(Request $request, FileUploader $fileUploader, ItemCollection $itemCollection): Response
    {

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
            $this->entityManager->persist($itemCollection);
            $this->entityManager->flush();
            $this->addFlash('success', 'Collection updated.');
        }

        return $this->render('collection/form.html.twig', [
            'action' => 'update',
            'form'  => $form,
            'collection' => $itemCollection,
        ]);
    }
}

