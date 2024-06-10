<?php

namespace App\Controller;

use App\Enum\PageSettingsEnum;
use App\Form\IssueType;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\TagRepository;
use DH\Adf\Node\Block\Document;
use Doctrine\ORM\EntityManagerInterface;
use JiraCloud\ADF\AtlassianDocumentFormat;
use JiraCloud\Issue\Issue;
use JiraCloud\Issue\IssueField;
use JiraCloud\Issue\IssueService;
use JiraCloud\Issue\RemoteIssueLink;
use JiraCloud\User\User;
use JiraCloud\User\UserService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'app')]
    public function index(Request $request, ItemCollectionRepository $itemCollectionRepository,ItemRepository $itemRepository, PaginatorInterface $paginator,TagRepository $tagRepository): Response
    {
        $tags = $tagRepository->findAll();
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
        $collections = array_slice($collections,0, PageSettingsEnum::CountCollectionsForMainPage->value);
        return $this->render('collection/index.html.twig', [
            'collections' => $collections,
            'items' => $items,
            'tags' => $tags,
        ]);
    }
    #[Route('/create-issue', name: 'app_create-issue')]
    public function createIssue(Request $request, ItemCollectionRepository $collectionRepository): Response
    {
        if(preg_match("/create-issue$/", $request->headers->get('referer'))===0){
            $referalURL = $request->headers->get('referer');
            setcookie('referalUrl', $referalURL);
        }
        $form = $this->createForm(IssueType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userEmail = $this->getUser()->getEmail();
            if (empty($this->findUserInJira($userEmail))) {
                $this->createUserInJira($userEmail);
                $this->addFlash('warning','Go to your email "'.$userEmail.'" and accept the invitation to sign up for Jira.');
            }
            $collectionName = "";
            $urlRequest = $request->headers->get('referer');
            if (isset($_COOKIE['referalUrl'])){
                $urlParsed = explode('/',$_COOKIE['referalUrl']);
                $urlRequest = $_COOKIE['referalUrl'];
                for ($i=0; $i<count($urlParsed); $i++) {
                    if ($urlParsed[$i] == 'collections' && $i !== count($urlParsed)-1) {
                        $collectionId = $urlParsed[$i+1];
                        $collectionName = $collectionRepository->find($collectionId)->getname();
                    }
                }
            }
            $issueName = $form->getData()['name'];
            $jiraResponse = $this->createIissueOnJira( $issueName, $urlRequest, $form->getData()['description'], $collectionName, $userEmail, $form->getData()['priority']);
            $issueLink = 'https://kol9di4.atlassian.net/jira/software/c/projects/TEST/issues/'.$jiraResponse->key;
            $issueId = $jiraResponse->id;
            $issue = new \App\Entity\Issue();
            $issue
                ->setName($issueName)
                ->setIdJira($issueId)
                ->setLink($issueLink);
            $this->entityManager->persist($issue);
            $this->getUser()->addIssue($issue);
            $this->entityManager->flush();
            setcookie('referalUrl', '', -1, '/');
            $this->addFlash('success','Issue "'.$issueName.'" created Successfully!');
            return $this->redirectToRoute('app');
        }

        return $this->render('issue/index.html.twig', [
            'form' => $form,
        ]);
    }

    protected function createIissueOnJira(string $name, string $link, string $description, string $collectionName, string $userEmail, string $issueType): Issue{
        $issueField = new IssueField();

        $doc = (new Document())
            ->heading(1)
            ->text('New bug')
            ->end()
            ->heading(4)
            ->text('Collection: ')
            ->strong($collectionName)
            ->end()
            ->heading(4)
            ->text("Page Link: ")
            ->text($link)
            ->end()
            ->heading(4)
            ->text('User email: ')
            ->strong($userEmail)
            ->end()
            ->paragraph()
            ->text('Description: ')
            ->text($description)
            ->end()
        ;

        $descV3 = new AtlassianDocumentFormat($doc);
        $issueField->setProjectKey('TEST')
            ->setSummary($name)
            ->setPriorityNameAsString($issueType)
            ->setIssueTypeAsString('Bug')
            ->setDescription($descV3)
        ;

        $issueService = new IssueService();

        $ret = $issueService->create($issueField);

        $ril = new RemoteIssueLink();

        $ril->setUrl($link)
            ->setTitle('Page Link')
        ;
        $issueService->createOrUpdateRemoteIssueLink($ret->id, $ril);
        return $ret;
    }
    protected function findUserInJira(string $email): array {
        $us = new UserService();
        $paramArray = [
            'query' => $email,
            'startAt' => 0,
            'maxResults' => 1000,
            'includeInactive' => true,
            //'property' => '*',
        ];
        return $us->findUsers($paramArray);
    }
    protected function createUserInJira(string $email): User {
        $us = new UserService();
        return $us->create([
            'emailAddress' => $email,
            'products' => ['jira-software']
        ]);
    }
}
