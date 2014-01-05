<?php

namespace Indeed\Feed\Tests\Request;

use Indeed\Feed\Request\Request;
use Indeed\Feed\Response\Response;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    protected $request;

    public function setUp()
    {
        $this->request = new Request();
    }

    public function testSetGetResponse()
    {
        $response = new Response;
        $this->request->setResponse($response);
        $this->assertEquals($response, $this->request->getResponse());
    }

    public function urlDataProvider()
    {
        return array(
            array(
                'string'
            ),
            array(
                123456
            ),
            array(
                array(
                    'value',
                    'value',
                    'value'
                )
            ),
        );
    }

    /**
     * @dataProvider urlDataProvider
     */
    public function testSetGetUrl($url)
    {
        if (!is_string($url)) { 
            $this->setExpectedException('Indeed\Feed\Exceptions\InvalidTypeException');
        }

        $this->assertInstanceOf(
            'Indeed\Feed\Request\Request',
            $this->request->setUrl($url)
        );
    }

    public function testSetOptions()
    {
        $data = array(
            'value',
            'value',
            'value'
        );

        $this->assertInstanceOf(
            'Indeed\Feed\Request\Request',
            $this->request->setOptions($data)
        );

        $this->assertEquals($data, $this->request->getOptions());
    }

    public function testSendRequest()
    {
        $response = new Response();
        $options = array(
            'publisher' => PUBLISHER_KEY,
            'q'         => 'php',
            'l'         => 'london',
            'co'        => 'gb',
            'v'         => 2,
            'limit'     => 9999
        );
        
        $url = 'http://api.indeed.com/ads/apisearch?';

        $result = $this->request
            ->setResponse($response)
            ->setOptions($options)
            ->setUrl($url)
            ->sendRequest();
        
        $this->assertInstanceOf(
            'Indeed\Feed\Response\ResponseInterface',
            $response
        );

    }
}