<?php

namespace App\Service;

use App\Entity\News;
use App\Message\ProcessFeeds;
use Doctrine\Persistence\ManagerRegistry;
use Monolog\Logger;
use PicoFeed\PicoFeedException;
use PicoFeed\Reader\Reader;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\MessageBusInterface;

class NewsParser
{

    private $logger,$bus;

    public function __construct(LoggerInterface $logger,MessageBusInterface $bus)
    {
        $this->logger = $logger;
        $this->bus = $bus;
    }
    public function getAvailableFeeds(){
        $reader = new Reader();
        $resource = $reader->download('https://www.punchng.com');

        $feeds = $reader->find(
            $resource->getUrl(),
            $resource->getContent()
        );

        print_r($feeds);
        return true;
    }

    public function discoverFeeds($url){
        try {
            $reader = new Reader;
            $resource = $reader->discover($url);

            $parser = $reader->getParser(
                $resource->getUrl(),
                $resource->getContent(),
                $resource->getEncoding()
            );

            return $parser->execute();
        }
        catch (PicoFeedException $e) {
            $this->logger->ERROR($e->getMessage());
        }
    }
    public function fetchNews($url)
    {
        $feed = $this->discoverFeeds($url);
        foreach ($feed->items as $item){
            $this->bus->dispatch(new ProcessFeeds($item));
        }
    }

}