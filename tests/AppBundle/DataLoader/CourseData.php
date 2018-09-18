<?php

namespace Tests\AppBundle\DataLoader;

class CourseData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => 'firstCourse',
            'level' => 1,
            'year' => 2016,
            'startDate' => "2016-09-04T00:00:00+00:00",
            'endDate' => "2017-01-01T00:00:00+00:00",
            'externalId' => 'first',
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => true,
            'school' => "1",
            'clerkshipType' => "1",
            'directors' => ['1'],
            'administrators' => ['1'],
            'cohorts' => ['1'],
            'terms' => ['1'],
            'objectives' => ['2'],
            'meshDescriptors' => ["abc1"],
            'learningMaterials' => ['1', '2', '4', '5', '6', '7', '8', '9', '10'],
            'sessions' => ['1', '2'],
            'descendants' => []
        );

        $arr[] = array(
            'id' => 2,
            'title' => 'course 2',
            'level' => 1,
            'year' => 2012,
            'startDate' => "2013-09-01T00:00:00+00:00",
            'endDate' => "2013-12-14T00:00:00+00:00",
            'externalId' => 'second',
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => false,
            'school' => "1",
            'clerkshipType' => "1",
            'directors' => ['2'],
            'administrators' => [],
            'cohorts' => ['1'],
            'terms' => ['1', '4'],
            'objectives' => ['2', '4'],
            'meshDescriptors' => [],
            'learningMaterials' => [],
            'sessions' => ['3', '5', '6', '7', '8'],
            'descendants' => []
        );

        $arr[] = array(
            'id' => 3,
            'title' => 'third',
            'level' => 1,
            'year' => 2012,
            'startDate' => "2013-09-01T00:00:00+00:00",
            'endDate' => "2013-12-14T00:00:00+00:00",
            'externalId' => 'course3',
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => false,
            'school' => "2",
            'directors' => ["4"],
            'administrators' => [],
            'cohorts' => ["2"],
            'terms' => [],
            'objectives' => ['5'],
            'meshDescriptors' => [],
            'learningMaterials' => [],
            'sessions' => [],
            'descendants' => ['4']
        );

        $arr[] = array(
            'id' => 4,
            'title' => 'fourth course',
            'level' => 3,
            'year' => 2013,
            'startDate' => "2013-09-01T00:00:00+00:00",
            'endDate' => "2013-12-14T00:00:00+00:00",
            'externalId' => 'fourth',
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => false,
            'school' => "2",
            'directors' => ["2"],
            'administrators' => [],
            'cohorts' => ["3"],
            'terms' => ['3', '6'],
            'objectives' => ['2'],
            'meshDescriptors' => [],
            'learningMaterials' => ["3"],
            'sessions' => ["4"],
            'ancestor' => '3',
            'descendants' => []
        );

        $arr[] = array(
            'id' => 5,
            'title' => 'fifth Course',
            'level' => 3,
            'year' => 2013,
            'startDate' => "2017-02-14T00:00:00+00:00",
            'endDate' => "2017-02-17T00:00:00+00:00",
            'externalId' => 'fifth',
            'locked' => true,
            'archived' => true,
            'publishedAsTbd' => true,
            'published' => true,
            'school' => "2",
            'directors' => [],
            'administrators' => ['4'],
            'cohorts' => ["3"],
            'terms' => [],
            'objectives' => [],
            'meshDescriptors' => [],
            'learningMaterials' => [],
            'sessions' => [],
            'descendants' => []
        );

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 6,
            'title' => $this->faker->text(25),
            'level' => 1,
            'year' => 2013,
            'startDate' => "2013-09-01T00:00:00+00:00",
            'endDate' => "2013-12-14T00:00:00+00:00",
            'externalId' => $this->faker->text(10),
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => false,
            'school' => "1",
            'clerkshipType' => "1",
            'directors' => [],
            'administrators' => [],
            'cohorts' => [],
            'terms' => [],
            'objectives' => [1],
            'meshDescriptors' => [],
            'learningMaterials' => [],
            'sessions' => [],
            'descendants' => []
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}