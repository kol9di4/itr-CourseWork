<?php

namespace App\Controller;

use App\Enum\PageSettings;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app')]
    public function index(Request $request, ItemCollectionRepository $itemCollectionRepository,ItemRepository $itemRepository, PaginatorInterface $paginator): Response
    {
        $items = $itemRepository->findAllSortedByDate();
        $collections = $itemCollectionRepository->findAll();

        $items = $paginator->paginate(
            $items, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        usort($collections, function($c1, $c2){
            return count($c1->getItems()) < count($c2->getItems());
        });
        $collections = array_slice($collections,0, PageSettings::CountCollectionsForMainPage->value);
        return $this->render('collection/index.html.twig', [
            'collections' => $collections,
            'items' => $items,
        ]);
    }
}
