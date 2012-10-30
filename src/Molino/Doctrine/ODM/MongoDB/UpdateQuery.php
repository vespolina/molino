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

use Molino\UpdateQueryInterface;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * The update query for Doctrine MongoDB ODM.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 * @author Richard Shank <develop@zestic.com>
 */
class UpdateQuery extends BaseQuery implements UpdateQueryInterface
{
    /**
     * {@inheritdoc}
     */
    protected function configureQueryBuilder(Builder $queryBuilder)
    {
        $queryBuilder->update();
    }

    /**
     * {@inheritdoc}
     */
    public function set($field, $value)
    {
        $this->getQueryBuilder()->set($field, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function inc($field, $inc)
    {
        $this->getQueryBuilder()->inc($field, $inc);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->getQueryBuilder()->getQuery()->execute();
    }
}
