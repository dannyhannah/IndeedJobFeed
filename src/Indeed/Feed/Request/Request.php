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
     *
     * @return Request returns instance of self for chaining
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Return the set response
     * 
     * @return null|ResponseInterface value of $this->
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set the request url
     * 
     * @param string $url url to use in request
     */
    public function setUrl($url)
    {
        if (!is_string($url)) {
            $message = sprintf('Invalid type given: %s required %s given', 'string', gettype($url));

            throw new InvalidTypeException($message);
        }

        $this->url = $url;

        return $this;
    }

    /**
     * Options to send in request
     * 
     * @param array $options array of options
     *
     * @return Request returns instance of self for chaining
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Options to send in request
     * 
     * @return array $options array of options
     *
     * @return Request returns instance of self for chaining
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Builds the request and sends response to the response object
     * 
     * @return Response  response object
     *
     * @return Request returns instance of self for chaining
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