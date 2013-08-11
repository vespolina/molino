<?php

namespace Model\Memory;

use Model\Memory\User;

class Article
{
    private $author;
    private $id;
    private $pages;
    private $title;

    public function getId()
    {
        return $this->id;
    }

    public function setAuthor(User $author)
    {
        $this->author = $author;

        return $this;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set the pages
     *
     * @param mixed $pages
     */
    public function setPages($pages)
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Return the pages
     *
     * @return mixed
     */
    public function getPages()
    {
        return $this->pages;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }
}
