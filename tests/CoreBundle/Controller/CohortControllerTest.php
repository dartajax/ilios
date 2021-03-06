<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Cohort controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CohortControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadCohortData',
            'Tests\CoreBundle\Fixture\LoadProgramYearData',
            'Tests\CoreBundle\Fixture\LoadCourseData',
            'Tests\CoreBundle\Fixture\LoadLearnerGroupData',
            'Tests\CoreBundle\Fixture\LoadUserData'
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
        ];
    }

    /**
     * @group controllers_a
     */
    public function testGetCohort()
    {
        $cohort = $this->container
            ->get('ilioscore.dataloader.cohort')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_cohorts',
                ['id' => $cohort['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($cohort),
            json_decode($response->getContent(), true)['cohorts'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllCohorts()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_cohorts'), null, $this->getAuthenticatedUserToken());
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.cohort')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['cohorts']
        );
    }

    /**
     * @group controllers_a
     */
    public function testPutCohort()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.cohort')
            ->getOne();
        $data['courses'] = ['3'];
        $data['users'] = ['3'];

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['learnerGroups']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_cohorts',
                ['id' => $data['id']]
            ),
            json_encode(['cohort' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['cohort']
        );
    }

    /**
     * @group controllers_a
     */
    public function testDeleteCohort()
    {
        $cohort = $this->container
            ->get('ilioscore.dataloader.cohort')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_cohorts',
                ['id' => $cohort['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_cohorts',
                ['id' => $cohort['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
    public function testCohortNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_cohorts', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }

    /**
     * @group controllers_a
     */
    public function testFilterByProgramYear()
    {
        $cohorts = $this->container->get('ilioscore.dataloader.cohort')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_cohorts', ['filters[programYear]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['cohorts'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $cohorts[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testFilterByCourse()
    {
        $cohorts = $this->container->get('ilioscore.dataloader.cohort')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_cohorts', ['filters[courses]' => 4]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['cohorts'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $cohorts[2]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testFilterByLearnerGroups()
    {
        $cohorts = $this->container->get('ilioscore.dataloader.cohort')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_cohorts', ['filters[learnerGroups]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['cohorts'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $cohorts[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testFilterByUsers()
    {
        $cohorts = $this->container->get('ilioscore.dataloader.cohort')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_cohorts', ['filters[users]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['cohorts'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $cohorts[0]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testFilterBySchools()
    {
        $cohorts = $this->container->get('ilioscore.dataloader.cohort')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_cohorts', ['filters[schools]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['cohorts'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $cohorts[3]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testFilterByStartYears()
    {
        $cohorts = $this->container->get('ilioscore.dataloader.cohort')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_cohorts', ['filters[startYears]' => ['2014', '2016']]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['cohorts'];
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $cohorts[1]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $cohorts[3]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostCohortFails()
    {
        $data = $this->container->get('ilioscore.dataloader.cohort')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['learnerGroups']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_cohorts'),
            json_encode(['cohort' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_GONE, $response->getStatusCode(), $response->getContent());
    }
}
