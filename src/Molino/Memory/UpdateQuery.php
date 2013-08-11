<?php

/*
 * This file is part of the Molino package.
 *
 * (c) 2013 Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Molino\Memory;

use Molino\UpdateQueryInterface;
use Molino\MolinoInterface;

/**
 * The update query for memory.
 *
 * @author Richard Shank <develop@zestic.com>
 */
class UpdateQuery extends BaseQuery implements UpdateQueryInterface
{
    private $changes;

    /**
     * {@inheritdoc}
     */
    public function __construct(MolinoInterface $molino, $modelClass)
    {
        parent::__construct($molino, $modelClass);

        $this->changes = array();
    }

    /**
     * {@inheritdoc}
     */
    public function set($field, $value)
    {
        $this->changes[$field] = array('Set' => $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function inc($field, $inc)
    {
        $this->changes[$field] = array('Inc' => $inc);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$results = $this->getData()) {
            return;
        }
        foreach ($results as $object) {
            foreach ($this->changes as $field => $changes) {
                $updater = 'update' . key($changes);
                $this->$updater($object, $field, $changes);
            }
        }
    }

    protected function updateSet($target, $field, $changes)
    {
        $rp = new \ReflectionProperty($target, $field);
        $rp->setAccessible(true);
        $rp->setValue($target, array_shift($changes));
        $rp->setAccessible(false);
    }

    protected function updateInc($target, $field, $changes)
    {
        $rp = new \ReflectionProperty($target, $field);
        $rp->setAccessible(true);
        $value = $rp->getValue($target);
        $rp->setValue($target, $value + array_shift($changes));
        $rp->setAccessible(false);
    }
}
