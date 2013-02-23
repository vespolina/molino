<?php

/*
 * This file is part of the Molino package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Molino\Doctrine\ODM\MongoDB;

use Molino\DeleteQueryInterface;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * The delete query for Doctrine ORM.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class DeleteQuery extends BaseQuery implements DeleteQueryInterface
{
    /**
     * {@inheritdoc}
     */
    protected function configureQueryBuilder(Builder $queryBuilder)
    {
        $queryBuilder->remove();
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->getQueryBuilder()->getQuery()->execute();
    }
}
