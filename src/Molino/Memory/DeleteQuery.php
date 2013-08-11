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

use Molino\DeleteQueryInterface;

/**
 * The delete query for memory.
 *
 * @author Richard Shank <develop@zestic.com>
 */
class DeleteQuery extends BaseQuery implements DeleteQueryInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$results = $this->getData()) {
            return;
        }

        $class = $this->getModelClass();
        $molino = $this->getMolino();
        $rp = new \ReflectionProperty($molino, 'data');
        $rp->setAccessible(true);
        $data = $rp->getValue($molino);
        foreach ($results as $id => $target) {
            unset($data[$class][$id]);
        }
        $rp->setValue($molino, $data);
        $rp->setAccessible(false);
    }
}
