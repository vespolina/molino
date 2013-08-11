<?php

namespace Molino\Tests\Memory;

use Molino\Memory\Molino;
use Molino\Memory\SelectQuery;

class SelectQueryTest extends TestCase
{
    private $query;

    protected function setUp()
    {
        parent::setUp();

        $this->molino = new Molino();
        $this->modelClass = 'Model\Memory\Article';
        $this->query = new SelectQuery($this->molino, $this->modelClass);
    }

    public function testFields()
    {
        $this->assertSame($this->query, $this->query->fields(array('foo', 'bar')));
        $rp = new \ReflectionProperty($this->query, 'fields');
        $rp->setAccessible(true);
        $fields = $rp->getValue($this->query);
        $this->assertSame(array('foo' => 1, 'bar' => 1), $fields);
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
        $rp = new \ReflectionProperty($this->query, 'sort');
        $rp->setAccessible(true);
        $sort = $rp->getValue($this->query);
        $this->assertSame(array('title' => 'asc'), $sort);
    }

    public function testSortAsc()
    {
        $this->assertSame($this->query, $this->query->sort('title', 'asc'));
        $rp = new \ReflectionProperty($this->query, 'sort');
        $rp->setAccessible(true);
        $sort = $rp->getValue($this->query);
        $this->assertSame(array('title' => 'asc'), $sort);
    }

    public function testSortDesc()
    {
        $this->assertSame($this->query, $this->query->sort('title', 'desc'));
        $rp = new \ReflectionProperty($this->query, 'sort');
        $rp->setAccessible(true);
        $sort = $rp->getValue($this->query);
        $this->assertSame(array('title' => 'desc'), $sort);
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
        $rp = new \ReflectionProperty($this->query, 'limit');
        $rp->setAccessible(true);
        $limit = $rp->getValue($this->query);
        $this->assertSame(10, $limit);
    }

    public function testSkip()
    {
        $this->assertSame($this->query, $this->query->skip(20));
        $rp = new \ReflectionProperty($this->query, 'skip');
        $rp->setAccessible(true);
        $skip = $rp->getValue($this->query);
        $this->assertSame(20, $skip);
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
        $articles = $this->loadArticles(10);
        $adapter = $this->query->createPagerfantaAdapter();
        $this->assertInstanceOf('Pagerfanta\Adapter\ArrayAdapter', $adapter);
        $this->assertEquals($articles, array_values($adapter->getArray()));
    }
}
