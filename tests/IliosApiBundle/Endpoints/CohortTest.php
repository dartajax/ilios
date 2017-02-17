<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * Cohort API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class CohortTest extends AbstractTest
{
    protected $testName =  'cohort';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCohortData',
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of field / value pairs to modify
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'programYear' => ['programYear', $this->getFaker()->text],
            'courses' => ['courses', [1]],
            'learnerGroups' => ['learnerGroups', [1]],
            'users' => ['users', [1]],
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of field / value pairs that are readOnly
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of filters to test
     * the key for each item is reflected in the failure message
     * the first item is an array of the positions the expected items
     * can be found in the data loader
     * the second item is the filter we are testing
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['filters[id]' => 1]],
            'title' => [[0], ['filters[title]' => 'test']],
            'programYear' => [[0], ['filters[programYear]' => 'test']],
            'courses' => [[0], ['filters[courses]' => [1]]],
            'learnerGroups' => [[0], ['filters[learnerGroups]' => [1]]],
            'users' => [[0], ['filters[users]' => [1]]],
        ];
    }

}