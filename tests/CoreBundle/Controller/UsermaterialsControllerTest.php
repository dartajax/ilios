<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;
use DateTime;
use Symfony\Component\Validator\Constraints\Date;

/**
 * UserRole controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class UsermaterialsControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData'
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
     * @group controllers_b
     */
    public function testGetMaterials()
    {
        $userId = 5;
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_usermaterials',
                ['id' => $userId]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $materials = json_decode($response->getContent(), true)['userMaterials'];
        $this->assertCount(4, $materials, 'All expected materials returned');
        $this->assertEquals('1', $materials[0]['id']);
        $this->assertEquals('1', $materials[0]['session']);
        $this->assertFalse($materials[0]['required']);
        $this->assertRegExp('/^firstlm/', $materials[0]['title']);
        $this->assertRegExp('/^desc1/', $materials[0]['description']);
        $this->assertRegExp('/^author1/', $materials[0]['originalAuthor']);
        $this->assertRegExp('/^citation1/', $materials[0]['citation']);
        $this->assertEquals('citation', $materials[0]['mimetype']);
        $this->assertRegExp('/^session1Title/', $materials[0]['sessionTitle']);
        $this->assertEquals('1', $materials[0]['course']);
        $this->assertRegExp('/^firstCourse/', $materials[0]['courseTitle']);
        $this->assertEquals('2016-09-08T15:00:00+00:00', $materials[0]['firstOfferingDate']);

        $this->assertEquals('1', $materials[1]['id']);
        $this->assertEquals('1', $materials[1]['course']);
        $this->assertFalse(array_key_exists('session', $materials[1]));
        $this->assertEquals('2', $materials[2]['id']);
        $this->assertEquals('1', $materials[2]['course']);
        $this->assertEquals('2016-09-04T00:00:00+00:00', $materials[2]['firstOfferingDate']);
        $this->assertFalse(array_key_exists('session', $materials[2]));
        $this->assertEquals('3', $materials[3]['id']);
        $this->assertEquals('1', $materials[3]['course']);
        $this->assertEquals('2016-09-04T00:00:00+00:00', $materials[2]['firstOfferingDate']);
        $this->assertFalse(array_key_exists('session', $materials[3]));
    }

    /**
     * @group controllers_b
     */
    public function testGetMaterialsBeforeTheBeginningOfTime()
    {
        $userId = 5;
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_usermaterials',
                ['id' => $userId, 'before' => 0]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $materials = json_decode($response->getContent(), true)['userMaterials'];
        $this->assertCount(0, $materials, 'No materials returned');
    }

    /**
     * @group controllers_b
     */
    public function testGetMaterialsAfterTheBeginningOfTime()
    {
        $userId = 5;
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_usermaterials',
                ['id' => $userId, 'after' => 0]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $materials = json_decode($response->getContent(), true)['userMaterials'];
        $this->assertCount(4, $materials, 'All materials returned');
    }

    /**
     * @group controllers_b
     */
    public function testGetMaterialsAfterTheEndOfTime()
    {
        $userId = 5;
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_usermaterials',
                ['id' => $userId, 'after' => 2051233745]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $materials = json_decode($response->getContent(), true)['userMaterials'];
        $this->assertCount(0, $materials, 'No materials returned');
    }

    /**
     * @group controllers_b
     */
    public function testGetMaterialsBeforeTheEndOfTime()
    {
        $userId = 5;
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_usermaterials',
                ['id' => $userId, 'before' => 2051233745]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $materials = json_decode($response->getContent(), true)['userMaterials'];
        $this->assertCount(4, $materials, 'All materials returned');
    }
}
