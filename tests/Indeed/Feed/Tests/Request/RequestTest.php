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

        $this->request->setUrl($url);
    }

    public function testSetOptions()
    {
        $data = array(
            'value',
            'value',
            'value'
        );

        $this->request->setOptions($data);
        $this->assertEquals($data, $this->request->getOptions());
    }
}