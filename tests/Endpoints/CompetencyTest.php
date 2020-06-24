<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\ReadWriteEndpointTest;

/**
 * Competency API endpoint Test.
 * @group api_5
 */
class CompetencyTest extends ReadWriteEndpointTest
{
    protected $testName =  'competencies';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadSchoolData',
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadCompetencyData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadSessionTypeData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadAamcPcrsData',
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
            'App\Tests\Fixture\LoadCourseObjectiveData',
            'App\Tests\Fixture\LoadProgramYearObjectiveData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'school' => ['school', 3],
            'parent' => ['parent', 2],
            'children' => ['children', [1], $skipped = true],
            'aamcPcrses' => ['aamcPcrses', ['aamc-pcrs-comp-c0102']],
            'programYears' => ['programYears', [2]],
            'active' => ['active', false],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'title' => [[2], ['title' => 'third competency']],
            'school' => [[0, 1, 2], ['school' => 1]],
            'schools' => [[0, 1, 2], ['schools' => [1]]],
            'parent' => [[2], ['parent' => 1], $skipped = true],
            'children' => [[0], ['children' => 3], $skipped = true],
            'aamcPcrses' => [[1], ['aamcPcrses' => ['aamc-pcrs-comp-c0101', 'aamc-pcrs-comp-c0102']], $skipped = true],
            'programYears' => [[0, 2], ['programYears' => [1]], $skipped = true],
            'notActive' => [[1], ['active' => false]],
            'active' => [[0, 2], ['active' => true]],
            'terms' => [[0, 2], ['terms' => [1, 2, 3]]],
            'sessions' => [[0, 2], ['sessions' => [1]]],
            'sessionTypes' => [[0, 2], ['sessionTypes' => [1]]],
            'courses' => [[0, 2], ['courses' => [1]]],
        ];
    }

    public function testPostCompetencyProgramYear()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'competencies', 'programYears');
    }

    public function testRemoveParent()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $data = $all[2];
        $this->assertArrayHasKey('parent', $data);
        $this->assertEquals('1', $data['parent']);
        $postData = $data;
        unset($postData['parent']);
        $this->putTest($data, $postData, $data['id']);
    }
}
