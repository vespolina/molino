<?php

namespace Molino\Tests\Doctrine\ODM\MongoDB;

use Model\Doctrine\ODM\MongoDB\Article;
use Molino\Doctrine\ODM\MongoDB\Molino;
use Molino\Tests\Doctrine\ODM\MongoDB\TestCase;

class MolinoTest extends TestCase
{
    private $molino;

    protected function setUp()
    {
        parent::setUp();

        $this->molino = new Molino($this->documentManager);
    }

    public function testGetDocumentManager()
    {
        $this->assertSame($this->documentManager, $this->molino->getDocumentManager());
    }

    public function testGetName()
    {
        $this->assertSame('doctrine_mongodb_odm', $this->molino->getName());
    }

    public function testCreate()
    {
        $model = $this->molino->create('Model\Doctrine\ODM\MongoDB\Article');
        $this->assertInstanceOf('Model\Doctrine\ODM\MongoDB\Article', $model);
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
        $this->documentManager->persist($article);
        $this->documentManager->flush();

        $this->documentManager->createQueryBuilder('Model\Doctrine\ODM\MongoDB\Article')
            ->field('id')->equals($article->getId())
            ->update()
            ->field('title')->set('bar')
            ->getQuery()
            ->execute();

        $this->molino->refresh($article);
        $this->assertSame('bar', $article->getTitle());
    }

    public function testDelete()
    {
        $article = new Article();
        $article->setTitle('foo');
        $this->documentManager->persist($article);
        $this->documentManager->flush();

        $id = $article->getId();
        $this->molino->delete($article);
        $this->assertNull($this->documentManager->find('Model\Doctrine\ODM\MongoDB\Article', $id));
    }

    public function testCreateSelectQuery()
    {
        $query = $this->molino->createSelectQuery('Model\Doctrine\ODM\MongoDB\Article');
        $this->assertInstanceOf('Molino\Doctrine\ODM\MongoDB\SelectQuery', $query);
        $this->assertSame($this->molino, $query->getMolino());
        $this->assertSame('Model\Doctrine\ODM\MongoDB\Article', $query->getModelClass());
    }

    public function testCreateUpdateQuery()
    {
        $query = $this->molino->createUpdateQuery('Model\Doctrine\ODM\MongoDB\Article');
        $this->assertInstanceOf('Molino\Doctrine\ODM\MongoDB\UpdateQuery', $query);
        $this->assertSame($this->molino, $query->getMolino());
        $this->assertSame('Model\Doctrine\ODM\MongoDB\Article', $query->getModelClass());
    }

    public function testCreateDeleteQuery()
    {
        $query = $this->molino->createDeleteQuery('Model\Doctrine\ODM\MongoDB\Article');
        $this->assertInstanceOf('Molino\Doctrine\ODM\MongoDB\DeleteQuery', $query);
        $this->assertSame($this->molino, $query->getMolino());
        $this->assertSame('Model\Doctrine\ODM\MongoDB\Article', $query->getModelClass());
    }

    public function testFindOneById()
    {
        $modelClass = 'Model\Doctrine\ODM\MongoDB\Article';
        $id = 10;
        $article = new Article();

        $documentManager = $this->getMockBuilder('Doctrine\ODM\MongoDB\DoctrineManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $documentManager
            ->expects($this->once())
            ->method('find')
            ->with($modelClass, $id)
            ->will($this->returnValue($article))
        ;

        $molino = new Molino($documentManager);
        $this->assertSame($article, $molino->findOneById($modelClass, $id));
    }
}
