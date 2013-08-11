<?php

namespace Molino\Tests\Memory;

use Molino\Memory\Molino;
use Molino\Memory\DeleteQuery;
use Molino\Memory\SelectQuery;

class DeleteQueryTest extends TestCase
{
    private $query;

    protected function setUp()
    {
        parent::setUp();

        $this->molino = new Molino();
        $this->modelClass = 'Model\Memory\Article';
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
