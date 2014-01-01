<?php

namespace Indeed\Feed\Request;

use Indeed\Feed\Response\ResponseInterface;
use Indeed\Feed\Exceptions\InvalidTypeException;

/**
 * Request object to handle any requests to an API
 *
 * @author Danny Hannah <danny@dannyhannah.co.uk>
 */
class Request implements RequestInterface
{
    protected $url;
    protected $options = array();
    protected $response;

    /**
     * Set the object which will handle the response from the API
     * 
     * @param ResponseInterface $response Object to handle response
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Set the request url
     * 
     * @param string $url url to use in request
     */
    public function setUrl($url)
    {
        if (!is_string($url)) {
            $message = sprintf('Invalid type given: %s required %s given', array('string', gettype($url)));
            
            throw new InvalidTypeException($message);
        }

        $this->url = $url;
    }

    /**
     * Options to send in request
     * 
     * @param array $options array of options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Builds the request and sends response to the response object
     * 
     * @return Response  response object
     */
    public function sendRequest()
    {
        $url = $this->url;
        $url .= http_build_query($this->options);

        // Build the curl request
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Save the result
        $result = curl_exec($ch);
        curl_close($ch);

        // Pass to the response object and return
        return $this->response->handleResponse($result);
    }
}