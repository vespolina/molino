<?php

namespace Molino\Tests\Memory;

use Model\Memory\Article;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $molino;

    protected function setUp()
    {

    }

    protected function tearDown()
    {

    }

    protected function loadArticles($nb)
    {
        $articles = array();
        for ($i = 1; $i <= $nb; $i++) {
            $article = new Article();
            $article->setTitle($i);
            $this->molino->save($article);
            $articles[] = $article;
        }

        return $articles;
    }
}
