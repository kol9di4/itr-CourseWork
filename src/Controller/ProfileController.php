<?php

namespace App\Controller;

use App\Repository\ItemCollectionRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile/{userName}', name: 'app_profile')]
    public function index(Request $request, ItemCollectionRepository $itemCollectionRepository, UserRepository $userRepository, PaginatorInterface $paginator, string $userName): Response
    {
        $user = $userRepository->findOneBy(['username' => $userName]);
        $collections = $itemCollectionRepository->findBy(['user' => $user]);
        usort($collections, function($c1, $c2){
            return count($c1->getItems()) < count($c2->getItems());
        });
        $collections = $paginator->paginate(
            $collections, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('profile/index.html.twig', [
            'collections' => $collections,
            'user' => $user,
        ]);
    }
}
