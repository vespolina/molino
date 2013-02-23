<?php

namespace Molino\Tests\Doctrine\ODM\MongoDB;

use Molino\Doctrine\ODM\MongoDB\Molino;
use Molino\Doctrine\ODM\MongoDB\UpdateQuery;
use Doctrine\ODM\MongoDB\Query\Builder;

class UpdateQueryTest extends TestCase
{
    private $query;

    protected function setUp()
    {
        parent::setUp();

        $this->molino = new Molino($this->documentManager);
        $this->modelClass = 'Model\Doctrine\ODM\MongoDB\Article';
        $this->query = new UpdateQuery($this->molino, $this->modelClass);
    }

    public function testSet()
    {
        $this->assertSame($this->query, $this->query->set('title', 'foo'));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(array('$set' => array('title' => 'foo')), $query['newObj']);
    }

    public function testInc()
    {
        $this->assertSame($this->query, $this->query->inc('age', 1));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(array('$inc' => array('age' => 1)), $query['newObj']);
    }

    public function testExecute()
    {
        $articles = $this->loadArticles(10);

        $this->query
            ->filterEqual('id', $articles[0]->getId())
            ->set('title', 'foo')
            ->execute()
        ;
        $this->documentManager->refresh($articles[0]);
        $this->assertSame('foo', $articles[0]->getTitle());
    }
}
