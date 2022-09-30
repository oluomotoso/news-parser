<?php

namespace App\Message;

final class ProcessFeeds
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

     private $feed;

     public function __construct(object $feed)
     {
         $this->feed = $feed;
     }

    public function getTitle(): string
    {
        return $this->feed->getTitle();
    }

    public function getDescription():string
    {
        return $this->feed->getContent();
    }

    public function getPublishedDate():\DateTimeImmutable
    {
         return $this->feed->getPublishedDate();
    }

    public function getUpdatedDate():\DateTimeImmutable
    {
        return $this->feed->getUpdatedDate();
    }

    public function getImage():string
    {
        return $this->feed->getEnclosureUrl();
    }

}
