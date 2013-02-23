<?php

namespace Molino\Tests\Doctrine\ODM\MongoDB;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $documentManager;

    protected function setUp()
    {
        $config = new \Doctrine\ODM\MongoDB\Configuration();
        $config->setAutoGenerateProxyClasses(true);
        $config->setProxyDir(\sys_get_temp_dir());
        $config->setHydratorDir(\sys_get_temp_dir());
        $config->setProxyNamespace('SymfonyTests\Doctrine');
        $config->setHydratorNamespace('SymfonyTests\Doctrine');
        $config->setMetadataDriverImpl(new AnnotationDriver(new AnnotationReader(), array())); // paths if needed
        $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache());

        $this->documentManager = DocumentManager::create(new Connection(), $config);

        AnnotationDriver::registerAnnotationClasses();
    }

    protected function tearDown()
    {
        $this->documentManager->getDocumentCollection('Model\Doctrine\ODM\MongoDB\Article')->drop();
    }

    protected function loadArticles($nb)
    {
        $articles = array();
        for ($i = 1; $i <= $nb; $i++) {
            $article = new \Model\Doctrine\ODM\MongoDB\Article();
            $article->setTitle($i);
            $this->documentManager->persist($article);
            $articles[] = $article;
        }
        $this->documentManager->flush();

        return $articles;
    }
}
