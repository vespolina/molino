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

use Molino\SelectQueryInterface;
use Pagerfanta\Adapter\ArrayAdapter;

/**
 * The select query for memory.
 *
 * @author Richard Shank <develop@zestic.com>
 */
class SelectQuery extends BaseQuery implements SelectQueryInterface
{
    protected $fields;
    protected $limit;
    protected $skip;
    protected $sort;

    /**
     * {@inheritdoc}
     */
    public function fields(array $fields)
    {
        $this->fields = array();
        foreach ($fields as $field) {
            if (!is_string($field)) {
                throw new \InvalidArgumentException('The fields must be strings.');
            }
            $this->fields[$field] = 1;
        }

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
        $this->sort[$field] = $order;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function skip($skip)
    {
        $this->skip = $skip;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function one()
    {
        $data = $this->getData();

        return is_array($data) ? array_shift($data) : null;
    }

    public function count()
    {
        return count($this->getData());
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->getData());
    }

    /**
     * {@inheritdoc}
     */
    public function createPagerfantaAdapter()
    {
        return new ArrayAdapter($this->getData());
    }
}
