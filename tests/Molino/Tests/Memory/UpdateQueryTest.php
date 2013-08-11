<?php

namespace Molino\Tests\Memory;

use Molino\Memory\Molino;
use Molino\Memory\UpdateQuery;

class UpdateQueryTest extends TestCase
{
    private $query;

    protected function setUp()
    {
        parent::setUp();

        $this->molino = new Molino();
        $this->modelClass = 'Model\Memory\Article';
        $this->query = new UpdateQuery($this->molino, $this->modelClass);
    }

    public function testSet()
    {
        $this->assertSame($this->query, $this->query->set('title', 'foo'));
        $this->assertSame(array('title' => array('Set' => 'foo')), $this->getChanges());
    }

    public function testInc()
    {
        $this->assertSame($this->query, $this->query->inc('pages', 1));
        $this->assertSame(array('pages' => array('Inc' => 1)), $this->getChanges());
    }

    public function testExecute()
    {
        $articles = $this->loadArticles(10);
        $id = $articles[0]->getId();
        $pages = $articles[0]->getPages();

        $this->query
            ->filterEqual('id', $articles[0]->getId())
            ->set('title', 'foo')
            ->inc('pages', 3)
            ->execute()
        ;

        $updatedArticle = $this->molino->findOneById('Model\Memory\Article', $id);
        $this->assertSame('foo', $updatedArticle->getTitle());
        $this->assertSame($pages + 3, $updatedArticle->getPages());
    }

    protected function getChanges()
    {
        $rp = new \ReflectionProperty($this->query, 'changes');
        $rp->setAccessible(true);
        $changes = $rp->getValue($this->query);
        $rp->setAccessible(false);

        return $changes;
    }
}
