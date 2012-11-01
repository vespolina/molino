<?php

namespace Model\Doctrine\ODM\MongoDB;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Model\Doctrine\ODM\MongoDB\User;

/**
 * @MongoDB\Document
 */
class Article
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\String
     */
    private $title;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Model\Doctrine\ODM\MongoDB\User")
     */
    private $author;

    public function getId()
    {
        return $this->id;
    }

    public function setAuthor(User $author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }
}
