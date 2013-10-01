<?php

/*
 * This file is part of the Molino package.
 *
 * (c) 2013 Vespolina Project http://www.vespolina-project.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Molino\Memory;

use Molino\MolinoInterface;

/**
 * The molino for memory.
 *
 * @author Richard Shank <develop@zestic.com>
 */
class Molino implements MolinoInterface
{
    private $data;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'memory';
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
        $class = get_class($model);

        if (!$id = $this->getProperty($model, 'id')) {
            $id = uniqid();
            $this->setProperty($model, 'id', $id);
        }
        $this->data[$class][$id] = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($model)
    {
        $class = get_class($model);
        $id = $model->getId();

        return $this->data[$class][$id];
    }

    /**
     * {@inheritdoc}
     */
    public function delete($model)
    {
        $class = get_class($model);
        $id = $model->getId();

        unset($this->data[$class][$id]);
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
        return isset($this->data[$modelClass][$id]) ? $this->data[$modelClass][$id] : null;
    }

    protected function setProperty($object, $property, $value)
    {
        $rp = new \ReflectionProperty($object, $property);
        $rp->setAccessible(true);
        $rp->setValue($object, $value);
        $rp->setAccessible(false);
    }

    protected function getProperty($object, $property)
    {
        $rp = new \ReflectionProperty($object, $property);
        $rp->setAccessible(true);
        $value = $rp->getValue($object);
        $rp->setAccessible(false);

        return $value;
    }
}
