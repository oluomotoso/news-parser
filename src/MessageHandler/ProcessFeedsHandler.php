<?php

namespace App\MessageHandler;

use App\Entity\News;
use App\Message\ProcessFeeds;
use App\Repository\NewsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ProcessFeedsHandler implements MessageHandlerInterface
{
    private $registry,$newsRepository;
    public function __construct(ManagerRegistry $registry,NewsRepository $newsRepository){
        $this->registry = $registry;
        $this->newsRepository = $newsRepository;
    }
    public function __invoke(ProcessFeeds $message)
    {

        $entityManager = $this->registry->getManager();
        $news = $this->newsRepository->findOneBy([
            'title' => $message->getTitle()
        ]);

        if (!$news) {
            $news = new News();
            $news->setTitle($message->getTitle());
            $news->setShortDescription($message->getDescription());
            $news->setImage($message->getImage());
            $news->setCreatedAt($message->getPublishedDate());
        }
        $news->setUpdatedAt($message->getUpdatedDate());
        $entityManager->persist($news);
        $entityManager->flush();
    }
}
