<?php

namespace Molino\Tests\Doctrine\ODM\MongoDB;

use Molino\Doctrine\ODM\MongoDB\Molino;
use Molino\Doctrine\ODM\MongoDB\SelectQuery;
use Doctrine\ODM\MongoDB\Query\Builder;

class SelectQueryTest extends TestCase
{
    private $query;

    protected function setUp()
    {
        parent::setUp();

        $this->molino = new Molino($this->documentManager);
        $this->modelClass = 'Model\Doctrine\ODM\MongoDB\Article';
        $this->query = new SelectQuery($this->molino, $this->modelClass);
    }

    public function testFields()
    {
        $this->assertSame($this->query, $this->query->fields(array('foo', 'bar')));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(array('foo,bar' => 1), $query['select']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFieldsNotString()
    {
        $this->query->fields(array('foo', 12));
    }

    public function testSort()
    {
        $this->assertSame($this->query, $this->query->sort('title'));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(array('title' => 1), $query['sort']);
    }

    public function testSortAsc()
    {
        $this->assertSame($this->query, $this->query->sort('title', 'asc'));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(array('title' => 1), $query['sort']);
    }

    public function testSortDesc()
    {
        $this->assertSame($this->query, $this->query->sort('title', 'desc'));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(array('title' => -1), $query['sort']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSortOrderNotValid()
    {
        $this->query->sort('name', 'no');
    }

    public function testLimit()
    {
        $this->assertSame($this->query, $this->query->limit(10));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(10, $query['limit']);
    }

    public function testSkip()
    {
        $this->assertSame($this->query, $this->query->skip(20));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(20, $query['skip']);
    }

    public function testAll()
    {
        $articles = $this->loadArticles(10);
        $this->assertEquals($articles, array_values($this->query->all()));
    }

    public function testOne()
    {
        $articles = $this->loadArticles(10);
        $this->assertContains($this->query->one(), $articles);
    }

    public function testOneNull()
    {
        $this->assertNull($this->query->one());
    }

    public function testCount()
    {
        $this->loadArticles(10);
        $this->assertSame(10, $this->query->count());
    }

    public function testGetIterator()
    {
        $articles = $this->loadArticles(10);
        $iterator = $this->query->getIterator();
        $this->assertInstanceOf('Traversable', $iterator);
        $this->assertSame($articles, array_values(iterator_to_array($iterator)));
    }

    public function testCreatePagerfantaAdapter()
    {
        $adapter = $this->query->createPagerfantaAdapter();
        $this->assertInstanceOf('Pagerfanta\Adapter\DoctrineODMMongoDBAdapter', $adapter);
        $this->assertEquals($this->query->getQueryBuilder()->getQuery(), $adapter->getQueryBuilder()->getQuery());
    }
}
