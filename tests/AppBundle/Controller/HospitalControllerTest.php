<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HospitalControllerTest extends WebTestCase
{
    public function testAddPatient()
    {
        $client = static::createClient();

        // Submit a raw JSON string in the request body
		$client->request(
		    'POST',
		    '/hospital/add/patient',
		    array(),
		    array(),
		    array('CONTENT_TYPE' => 'application/json'),
		    '{ "name":"Stefan", "dob":"15-02-1982", "gender":1, "hospital_id":21, "doctor_id":15 }'
		);

        $this->assertEquals(303, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect());
    }


    public function testDoctorDetail()
    {
    	$client = static::createClient();

    	$client->request(
		    'GET',
		    '/hospital/doctor/15',
		    array(),
		    array(),
		    array(
		        'CONTENT_TYPE' => 'application/json',
		        'HTTP_REFERER' => '/hospital/add/patient',
		    )
		);

		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertJsonResponse($client->getResponse(), Response::HTTP_OK);
    }
}