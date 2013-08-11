<?php

namespace Molino\Tests\Memory;

use Model\Memory\Article;
use Model\Memory\User;
use Molino\Memory\Molino;
use Molino\Memory\BaseQuery as OriginalBaseQuery;
use Molino\Memory\SelectQuery;

class BaseQuery extends OriginalBaseQuery
{
}

class BaseQueryTest extends TestCase
{
    private $query;

    protected function setUp()
    {
        parent::setUp();

        $this->molino = new Molino();
        $this->modelClass = 'Model\Memory\Article';
        $this->query = new SelectQuery($this->molino, $this->modelClass);
        $this->loadTestArticles();
    }

    public function testFilterEqual()
    {
        $this->assertSame($this->query, $this->query->filterEqual('title', 'foo'));
        $this->assertSame(array('title' => array('Equal' => 'foo')), $this->query->getCriteria());
        $results = $this->query->all();
        $this->assertCount(1, $results);
    }

    public function testFilterNotEqual()
    {
        $this->assertSame($this->query, $this->query->filterNotEqual('title', 'foo'));
        $this->assertSame(array('title' => array('NotEqual' => 'foo')), $this->query->getCriteria());
        $results = $this->query->all();
        $this->assertCount(5, $results);
    }

    /**
     * @dataProvider filterLikeProvider
     */
    public function testFilterLike($value, $pattern, $matches)
    {
        $this->assertSame($this->query, $this->query->filterLike('title', $value));
        $this->assertEquals(array('title' => array('Like' => $pattern)), $this->query->getCriteria());
        $results = $this->query->all();
        $this->assertCount($matches, $results);
    }

    /**
     * @dataProvider filterLikeProvider
     */
    public function testFilterNotLike($value, $pattern, $matches)
    {
        $this->assertSame($this->query, $this->query->filterNotLike('title', $value));
        $this->assertEquals(array('title' => array('NotLike' => $pattern)), $this->query->getCriteria());
        $results = $this->query->all();
        $this->assertCount(6 - $matches, $results);
    }

    public function filterLikeProvider()
    {
        return array(
            array('foo', '/^foo$/', 1),
            array('*foo', '/.*foo$/', 2),
            array('foo*', '/^foo.*/', 2),
            array('*foo*', '/.*foo.*/', 4),
            array('f*oo', '/^f.*oo$/', 2),
        );
    }

    public function testFilterIn()
    {
        $this->assertSame($this->query, $this->query->filterIn('title', array('foo', 'bar')));
        $this->assertSame(array('title' => array('In' => array('foo', 'bar'))), $this->query->getCriteria());
        $results = $this->query->all();
        $this->assertCount(2, $results);
    }

    public function testFilterNotIn()
    {
        $this->assertSame($this->query, $this->query->filterNotIn('title', array('foo', 'bar')));
        $this->assertSame(array('title' => array('NotIn' => array('foo', 'bar'))), $this->query->getCriteria());
        $results = $this->query->all();
        $this->assertCount(4, $results);
    }

    public function testFilterGreater()
    {
        $this->assertSame($this->query, $this->query->filterGreater('pages', 20));
        $this->assertSame(array('pages' => array('Greater' => 20)), $this->query->getCriteria());
        $results = $this->query->all();
        $this->assertCount(3, $results);
    }

    public function testFilterLess()
    {
        $this->assertSame($this->query, $this->query->filterLess('pages', 20));
        $this->assertSame(array('pages' => array('Less' => 20)), $this->query->getCriteria());
        $results = $this->query->all();
        $this->assertCount(2, $results);
    }

    public function testFilterGreaterEqual()
    {
        $this->assertSame($this->query, $this->query->filterGreaterEqual('pages', 20));
        $this->assertSame(array('pages' => array('GreaterEqual' => 20)), $this->query->getCriteria());
        $results = $this->query->all();
        $this->assertCount(4, $results);
    }

    public function testFilterLessEqual()
    {
        $this->assertSame($this->query, $this->query->filterLessEqual('pages', 20));
        $this->assertSame(array('pages' => array('LessEqual' => 20)), $this->query->getCriteria());
        $results = $this->query->all();
        $this->assertCount(3, $results);
    }

    protected function loadTestArticles()
    {
        $articles = array(
            array('title' => 'foo', 'author' => 'Smith', 'pages' => 10),
            array('title' => 'bar', 'author' => 'Jones', 'pages' => 15),
            array('title' => 'tofoo', 'author' => 'Johnson', 'pages' => 20),
            array('title' => 'foos', 'author' => 'Williams', 'pages' => 25),
            array('title' => 'freefood', 'author' => 'Brown', 'pages' => 30),
            array('title' => 'froo', 'author' => 'Davis', 'pages' => 35),
        );
        foreach ($articles as $data) {
            $author = new User();
            $author->setName($data['author']);
            $article = new Article();
            $article
                ->setAuthor($author)
                ->setPages($data['pages'])
                ->setTitle($data['title']);
            $this->molino->save($article);
        }
    }
}
