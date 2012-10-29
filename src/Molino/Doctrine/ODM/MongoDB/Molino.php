<?php

namespace Molino\Doctrine\ODM\MongoDB;

use Molino\MolinoInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * The molino for Doctrine MongoDB ODM.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 * @author Richard Shank <develop@zestic.com>
 */
class Molino implements MolinoInterface
{
    private $documentManager;

    /**
     * Constructor.
     *
     * @param DocumentManager A document manager.
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * Returns the document manager.
     *
     * @return DocumentManager The document manager.
     */
    public function getDocumentManager()
    {
        return $this->documentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'doctrine_mongodb_odm';
    }

    /**
     * {@inheritdoc}
     */
    public function create($class)
    {
        return new $class;
    }

    /**
     * {@inheritdoc}
     */
    public function save($model)
    {
        $this->documentManager->persist($model);
        $this->documentManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($model)
    {
        $this->documentManager->refresh($model);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($model)
    {
        $this->documentManager->remove($model);
        $this->documentManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createSelectQuery($modelClass)
    {
        return new SelectQuery($this, $modelClass);
    }

    /**
     * {@inheritdoc}
     */
    public function createUpdateQuery($modelClass)
    {
        return new UpdateQuery($this, $modelClass);
    }

    /**
     * {@inheritdoc}
     */
    public function createDeleteQuery($modelClass)
    {
        return new DeleteQuery($this, $modelClass);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneById($modelClass, $id)
    {
        return $this->documentManager->find($modelClass, $id);
    }
}
