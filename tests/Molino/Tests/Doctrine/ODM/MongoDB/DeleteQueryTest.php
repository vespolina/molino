<?php

namespace Molino\Tests\Doctrine\ODM\MongoDB;

use Molino\Doctrine\ODM\MongoDB\Molino;
use Molino\Doctrine\ODM\MongoDB\DeleteQuery;
use Molino\Doctrine\ODM\MongoDB\SelectQuery;
use Doctrine\ODM\MongoDB\Query\Builder;

class DeleteQueryTest extends TestCase
{
    private $query;

    protected function setUp()
    {
        parent::setUp();

        $this->molino = new Molino($this->documentManager);
        $this->modelClass = 'Model\Doctrine\ODM\MongoDB\Article';
        $this->query = new DeleteQuery($this->molino, $this->modelClass);
    }

    public function testExecute()
    {
        $this->loadArticles(10);
        $this->query->execute();

        $selectQuery = new SelectQuery($this->molino, $this->modelClass);
        $this->assertSame(0, $selectQuery->count());
    }
}
