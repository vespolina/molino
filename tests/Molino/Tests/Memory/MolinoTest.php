<?php

namespace Molino\Tests\Memory;

use Model\Memory\Article;
use Molino\Memory\Molino;
use Molino\Tests\Memory\TestCase;

class MolinoTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->molino = new Molino();
    }

    public function testGetName()
    {
        $this->assertSame('memory', $this->molino->getName());
    }

    public function testCreate()
    {
        $model = $this->molino->create('Model\Memory\Article');
        $this->assertInstanceOf('Model\Memory\Article', $model);
    }

    public function testSave()
    {
        $article = new Article();
        $article->setTitle('foo');
        $this->molino->save($article);
        $this->assertNotNull($article->getId());
    }

    public function testRefresh()
    {
        $article = new Article();
        $article->setTitle('foo');
        $this->molino->save($article);
        $id = $article->getId();

        $article->setTitle('bar');
        $this->molino->save($article);
        $this->assertSame($id, $article->getId());

        $this->molino->refresh($article);
        $this->assertSame('bar', $article->getTitle());
    }

    public function testDelete()
    {
        $article = new Article();
        $article->setTitle('foo');
        $this->molino->save($article);

        $id = $article->getId();
        $this->molino->delete($article);
        $rp = new \ReflectionProperty($this->molino, 'data');
        $rp->setAccessible(true);
        $data = $rp->getValue($this->molino);
        $this->assertArrayNotHasKey($id, $data['Model\Memory\Article']);
    }

    public function testCreateSelectQuery()
    {
        $query = $this->molino->createSelectQuery('Model\Memory\Article');
        $this->assertInstanceOf('Molino\Memory\SelectQuery', $query);
        $this->assertSame($this->molino, $query->getMolino());
        $this->assertSame('Model\Memory\Article', $query->getModelClass());
    }

    public function testCreateUpdateQuery()
    {
        $query = $this->molino->createUpdateQuery('Model\Memory\Article');
        $this->assertInstanceOf('Molino\Memory\UpdateQuery', $query);
        $this->assertSame($this->molino, $query->getMolino());
        $this->assertSame('Model\Memory\Article', $query->getModelClass());
    }

    public function testCreateDeleteQuery()
    {
        $query = $this->molino->createDeleteQuery('Model\Memory\Article');
        $this->assertInstanceOf('Molino\Memory\DeleteQuery', $query);
        $this->assertSame($this->molino, $query->getMolino());
        $this->assertSame('Model\Memory\Article', $query->getModelClass());
    }

    public function testFindOneById()
    {
        $article = new Article();
        $this->molino->save($article);
        $id = $article->getId();

        $modelClass = 'Model\Memory\Article';
        $this->assertSame($article, $this->molino->findOneById($modelClass, $id));
    }
}
