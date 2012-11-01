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

use Molino\BaseQuery as BaseBaseQuery;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * The base query for Doctrine MongoDB ODM.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class BaseQuery extends BaseBaseQuery
{
    private $queryBuilder;
    private $lastParameterId = 0;

    /**
     * Returns the query builder.
     *
     * @return Doctrine\ODM\MongoDB\Query\Builder The query builder.
     */
    public function getQueryBuilder()
    {
        if (null === $this->queryBuilder) {
            $queryBuilder = $this
                ->getMolino()
                ->getDocumentManager()
                ->createQueryBuilder($this->getModelClass())
            ;
            $this->configureQueryBuilder($queryBuilder);
            $this->queryBuilder = $queryBuilder;
        }

        return $this->queryBuilder;
    }

    /**
     * Method to configure the query builder.
     *
     * @param Doctrine\ODM\MongoDB\Query\Builder The query builder.
     */
    protected function configureQueryBuilder(Builder $queryBuilder)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function filterEqual($field, $value)
    {
        if (is_object($value)) {
            $this->getQueryBuilder()->field($field)->references($value);
        } else {
            $this->andWhere('equals', $field, $value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterNotEqual($field, $value)
    {
        $this->andWhere('notEqual', $field, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLike($field, $value)
    {
        $this->andWhere('equals', $field, new \MongoRegex($this->buildLikePattern($value)));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterNotLike($field, $value)
    {
        $this->andWhere('notEqual', $field, new \MongoRegex($this->buildLikePattern($value)));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterIn($field, array $values)
    {
        $this->andWhere('in', $field, $values);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterNotIn($field, array $values)
    {
        $this->andWhere('notIn', $field, $values);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterGreater($field, $value)
    {
        $this->andWhere('gt', $field, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLess($field, $value)
    {
        $this->andWhere('lt', $field, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterGreaterEqual($field, $value)
    {
        $this->andWhere('gte', $field, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLessEqual($field, $value)
    {
        $this->andWhere('lte', $field, $value);

        return $this;
    }

    private function andWhere($comparison, $field, $value)
    {
        $this->getQueryBuilder()->field($field)->$comparison($value);
    }

    private function buildLikePattern($value)
    {
        $start = false;
        $pattern = '';
        $end = false;

        $parsed = $this->parseLike($value);
        reset($parsed);
        if ('*' !== current($parsed)) {
            $start = true;
        }
        end($parsed);
        if ('*' !== current($parsed)) {
            $end = true;
        }

        foreach ($parsed as $v) {
            if ('*' === $v) {
                $pattern .= '.*';
            } else {
                $pattern .= $v;
            }
        }

        if ($start) {
            $pattern = '^'.$pattern;
        }
        if ($end) {
            $pattern .= '$';
        }

        $pattern = '/'.$pattern.'/';

        return $pattern;
    }
}
