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

use Molino\SelectQueryInterface;
use Doctrine\ODM\MongoDB\Query\Builder;
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;

/**
 * The select query for Doctrine ORM.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class SelectQuery extends BaseQuery implements SelectQueryInterface
{
    /**
     * {@inheritdoc}
     */
    public function fields(array $fields)
    {
        foreach ($fields as &$field) {
            if (!is_string($field)) {
                throw new \InvalidArgumentException('Fields must be strings.');
            }
        }
        $fieldList = implode(',', $fields);
        $this->getQueryBuilder()->select($fieldList);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function sort($field, $order = 'asc')
    {
        if (!in_array($order, array('asc', 'desc'))) {
            throw new \InvalidArgumentException(sprintf('The order "%s" is not valid.', $order));
        }
        $this->getQueryBuilder()->sort($field, $order);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function limit($limit)
    {
        $this->getQueryBuilder()->limit($limit);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function skip($skip)
    {
        $this->getQueryBuilder()->skip($skip);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->getQueryBuilder()->getQuery()->execute()->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function one()
    {
        return $this->getQueryBuilder()->getQuery()->getSingleResult();
    }

    public function count()
    {
        return $this->getQueryBuilder()->getQuery()->count();
    }

    public function getIterator()
    {
        return $this->getQueryBuilder()->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function createPagerfantaAdapter()
    {
        return new DoctrineODMMongoDBAdapter($this->getQueryBuilder());
    }
}
