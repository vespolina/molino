<?php

namespace Molino\Tests\Doctrine\ODM\MongoDB;

use Model\Doctrine\ODM\MongoDB\User;
use Molino\Doctrine\ODM\MongoDB\Molino;
use Molino\Doctrine\ODM\MongoDB\BaseQuery as OriginalBaseQuery;

class BaseQuery extends OriginalBaseQuery
{
}

class BaseQueryTest extends TestCase
{
    private $query;

    protected function setUp()
    {
        parent::setUp();

        $this->molino = new Molino($this->documentManager);
        $this->modelClass = 'Model\Doctrine\ODM\MongoDB\Article';
        $this->query = new BaseQuery($this->molino, $this->modelClass);
    }

    public function testGetQueryBuilder()
    {
        $queryBuilder = $this->query->getQueryBuilder();
        $this->assertInstanceOf('Doctrine\ODM\MongoDB\Query\Builder', $queryBuilder);
    }

    public function testFilterEqual()
    {
        $this->assertSame($this->query, $this->query->filterEqual('title', 'foo'));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(array('title' => 'foo'), $query['query']);
    }

    public function testFilterEqualObject()
    {
        $user = new User();
        $this->assertSame($this->query, $this->query->filterEqual('author', $user));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(array('author.$id' => null), $query['query']);
    }

    public function testFilterNotEqual()
    {
        $this->assertSame($this->query, $this->query->filterNotEqual('title', 'foo'));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(array('title' => array('$ne' => 'foo')), $query['query']);
    }

    /**
     * @dataProvider filterLikeProvider
     */
    public function testFilterLike($value, $like)
    {
        $this->assertSame($this->query, $this->query->filterLike('title', $value));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertEquals(array('title' => new \MongoRegex($like)), $query['query']);
    }

    /**
     * @dataProvider filterLikeProvider
     */
    public function testFilterNotLike($value, $like)
    {
        $this->assertSame($this->query, $this->query->filterNotLike('title', $value));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertEquals(array('title' => array('$ne' => new \MongoRegex($like))), $query['query']);
    }

    public function filterLikeProvider()
    {
        return array(
            array('foo', '/^foo$/'),
            array('*foo', '/.*foo$/'),
            array('foo*', '/^foo.*/'),
            array('*foo*', '/.*foo.*/'),
            array('f*oo', '/^f.*oo$/'),
        );
    }

    public function testFilterIn()
    {
        $this->assertSame($this->query, $this->query->filterIn('title', array('foo', 'bar')));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(array('title' => array('$in' => array('foo', 'bar'))), $query['query']);
    }

    public function testFilterNotIn()
    {
        $this->assertSame($this->query, $this->query->filterNotIn('title', array('foo', 'bar')));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(array('title' => array('$nin' => array('foo', 'bar'))), $query['query']);
    }

    public function testFilterGreater()
    {
        $this->assertSame($this->query, $this->query->filterGreater('age', 20));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(array('age' => array('$gt' => 20)), $query['query']);
    }

    public function testFilterLess()
    {
        $this->assertSame($this->query, $this->query->filterLess('age', 20));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(array('age' => array('$lt' => 20)), $query['query']);
    }

    public function testFilterGreaterEqual()
    {
        $this->assertSame($this->query, $this->query->filterGreaterEqual('age', 20));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(array('age' => array('$gte' => 20)), $query['query']);
    }

    public function testFilterLessEqual()
    {
        $this->assertSame($this->query, $this->query->filterLessEqual('age', 20));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $this->assertSame(array('age' => array('$lte' => 20)), $query['query']);
    }

    /**
     * @dataProvider filterNearProvider
     */
    public function testFilterNear($unit, $radius)
    {
        $this->assertSame($this->query, $this->query->filterNear('coordinates', -122.408, 45.4941, 25, $unit));
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $expected = array(
            'coordinates' => array(
                '$geoWithin' => array(
                    '$centerSphere' => array(
                        array(-122.408, 45.4941),
                        $radius
                    ),
                )
            )
        );
        $this->assertEquals($expected, $query['query']);
    }

    public function filterNearProvider()
    {
        return array(
            array('km', 0.0039240307644012),
            array('kilometer', 0.0039240307644012),
            array('mi', 0.00631472594089),
            array('mile', 0.00631472594089),
        );
    }

    public function testSeveral()
    {
        $this->query
            ->filterEqual('title', 'foo')
            ->filterNotEqual('content', 'bar')
            ->filterIn('author', array(1, 2))
        ;
        $query = $this->query->getQueryBuilder()->getQuery()->getQuery();
        $expected = array(
            'title' => 'foo',
            'content' => array(
                '$ne' => 'bar',
            ),
            'author' => array(
                '$in' => array(
                    1, 2
                )
            ),
        );
        $this->assertSame($expected, $query['query']);
    }
}
