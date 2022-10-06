<?php

namespace App\Controller;

use App\Entity\News;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    /**
     * @Route("/news", name="app_news",defaults={"page":1},methods={"GET"})
     * @Route("news/page/{page}",methods={"GET"},name="app_news_paginated")
     */
    public function index(NewsRepository $news,int $page=1): Response
    {
        $lNews= $news->fetchLatestNews($page);
        return $this->render('news/index.html.twig', [
            'paginate'=> $lNews
        ]);
    }


    /**
     * Deletes a Post entity.
     * @Route("/news/{id}/delete", name= "app_news_delete", methods= {"POST"})
     * @IsGranted("ROLE_ADMIN",message="Unable to perform operation",statusCode=401)
     */
    public function delete(Request $request, News $news, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('app_news');
        }

        $entityManager->remove($news);
        $entityManager->flush();
        $this->addFlash('success', 'news deleted successfully');
        return $this->redirectToRoute('app_news');
    }
}
