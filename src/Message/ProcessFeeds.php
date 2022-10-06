<?php

namespace App\Message;

final class ProcessFeeds
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

     private $feed;

     public function __construct(array $feed)
     {
         $this->feed = $feed;
     }

    public function getTitle(): string
    {
        return $this->feed['title'];
    }

    public function getDescription():string
    {
        return $this->feed['description'];
    }

    public function getPublishedDate():\DateTimeImmutable
    {
         return \DateTimeImmutable::createFromMutable($this->feed['publishedDate']);
    }

    public function getUpdatedDate():\DateTimeImmutable
    {
        return \DateTimeImmutable::createFromMutable($this->feed['updatedDate']);
    }

    public function getImage():string
    {
        return $this->feed['image'];
    }

}
