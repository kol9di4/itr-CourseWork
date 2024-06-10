<?php

namespace App\Controller;

use App\Repository\IssueRepository;
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
            $collections,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('profile/index.html.twig', [
            'collections' => $collections,
            'user' => $user,
        ]);
    }
    #[Route('/profile/{userName}/issues', name: 'app_issues')]
    public function issues(Request $request, IssueRepository $issueRepository, UserRepository $userRepository, PaginatorInterface $paginator, string $userName): Response
    {
        $user = $userRepository->findOneBy(['username' => $userName]);
        if ($user !== $this->getUser() || !isset($user)) {
            $this->addFlash('danger','Access only for user "'.$userName.'"!');
            return $this->redirectToRoute('app');
        }
        $issues = $issueRepository->findIssuesOrderAsc($user);
        $issues = $paginator->paginate(
            $issues,
            $request->query->getInt('page', 1),
            10,
        );
        return $this->render('profile/issues.html.twig', [
            'issues' => $issues,
            'user' => $user,
        ]);
    }
}
