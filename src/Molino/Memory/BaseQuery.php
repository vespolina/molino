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

use Molino\BaseQuery as BaseBaseQuery;
use Molino\MolinoInterface;

/**
 * The base query for memory.
 *
 * @author Richard Shank <develop@zestic.com>
 */
abstract class BaseQuery extends BaseBaseQuery
{
    protected $criteria;

    /**
     * {@inheritdoc}
     */
    public function __construct(MolinoInterface $molino, $modelClass)
    {
        parent::__construct($molino, $modelClass);

        $this->criteria = array();
    }

    /**
     * {@inheritdoc}
     */
    public function setMolino(MolinoInterface $molino)
    {
        if (!$molino instanceof Molino) {
            throw new \InvalidArgumentException('The molino must be an instance of Molino\Mandango\Molino.');
        }

        parent::setMolino($molino);
    }

    /**
     * Returns the criteria.
     *
     * @return array The criteria.
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * {@inheritdoc}
     */
    public function filterEqual($field, $value)
    {
        $this->criteria[$field] = array('Equal' => $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterNotEqual($field, $value)
    {
        $this->criteria[$field] = array('NotEqual' => $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLike($field, $value)
    {
        $pattern = $this->buildLikePattern($value);
        $this->criteria[$field] = array('Like' => $pattern);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterNotLike($field, $value)
    {
        $pattern = $this->buildLikePattern($value);

        $this->criteria[$field] = array('NotLike' => $pattern);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterIn($field, array $values)
    {
        $this->criteria[$field] = array('In' => $values);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterNotIn($field, array $values)
    {
        $this->criteria[$field] = array('NotIn' => $values);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterGreater($field, $value)
    {
        $this->criteria[$field] = array('Greater' => $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLess($field, $value)
    {
        $this->criteria[$field] = array('Less' => $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterGreaterEqual($field, $value)
    {
        $this->criteria[$field] = array('GreaterEqual' => $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLessEqual($field, $value)
    {
        $this->criteria[$field] = array('LessEqual' => $value);

        return $this;
    }

    protected function getData($class = null)
    {
        $molino = $this->getMolino();
        $rp = new \ReflectionProperty($molino, 'data');
        $rp->setAccessible(true);
        $data = $rp->getValue($molino);
        $rp->setAccessible(false);
        $class = $class ? $class : $this->getModelClass();

        return $this->filterResults($data[$class]);
    }

    protected function filterResults($data)
    {
        foreach ($this->criteria as $field => $rules) {
            $matcher = 'match' . key($rules);
            foreach ($data as $id => $target) {
                if (!$this->$matcher($target, $field, $rules)) {
                    unset($data[$id]);
                }
            }
        }

        return count($data) ? $data : null;
    }

    protected function matchEqual($target, $field, $rules)
    {
        $rp = new \ReflectionProperty($target, $field);
        $rp->setAccessible(true);
        $value = $rp->getValue($target);

        return $value == array_shift($rules);
    }

    protected function matchNotEqual($target, $field, $rules)
    {
        $rp = new \ReflectionProperty($target, $field);
        $rp->setAccessible(true);
        $value = $rp->getValue($target);

        return $value != array_shift($rules);
    }

    protected function matchLike($target, $field, $rules)
    {
        $rp = new \ReflectionProperty($target, $field);
        $rp->setAccessible(true);
        $value = $rp->getValue($target);
        $pattern = array_shift($rules);

        return (bool) preg_match($pattern, $value);
    }

    protected function matchNotLike($target, $field, $rules)
    {
        $rp = new \ReflectionProperty($target, $field);
        $rp->setAccessible(true);
        $value = $rp->getValue($target);
        $pattern = array_shift($rules);

        return !(bool) preg_match($pattern, $value);
    }

    protected function matchIn($target, $field, $rules)
    {
        $rp = new \ReflectionProperty($target, $field);
        $rp->setAccessible(true);
        $value = $rp->getValue($target);

        return in_array($value, array_shift($rules));
    }

    protected function matchNotIn($target, $field, $rules)
    {
        $rp = new \ReflectionProperty($target, $field);
        $rp->setAccessible(true);
        $value = $rp->getValue($target);

        return !in_array($value, array_shift($rules));
    }

    protected function matchGreater($target, $field, $rules)
    {
        $rp = new \ReflectionProperty($target, $field);
        $rp->setAccessible(true);
        $value = $rp->getValue($target);

        return $value > array_shift($rules);
    }

    protected function matchLess($target, $field, $rules)
    {
        $rp = new \ReflectionProperty($target, $field);
        $rp->setAccessible(true);
        $value = $rp->getValue($target);

        return $value < array_shift($rules);
    }

    protected function matchGreaterEqual($target, $field, $rules)
    {
        $rp = new \ReflectionProperty($target, $field);
        $rp->setAccessible(true);
        $value = $rp->getValue($target);

        return $value >= array_shift($rules);
    }

    protected function matchLessEqual($target, $field, $rules)
    {
        $rp = new \ReflectionProperty($target, $field);
        $rp->setAccessible(true);
        $value = $rp->getValue($target);

        return $value <= array_shift($rules);
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
