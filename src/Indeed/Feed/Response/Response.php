<?php

namespace Indeed\Feed\Response;

use Indeed\Feed\Exceptions\InvalidResponseException;

/**
 * Simple response object to handle the request response from the indeed api
 *
 * @author Danny Hannah <danny@dannyhannah.co.uk>
 */
class Response implements ResponseInterface
{
    protected $response;

    /**
     * Return the response
     * 
     * @return SimpleXMLElement object of results
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Handle the response data from the API request
     * 
     * @param  string   $result xml returned as string from the API
     * 
     * @return Response         returns instance of itself
     */
    public function handleResponse($result)
    {
        $result = new \SimpleXMLElement($result);

        // Check there isn't an error returned
        if (property_exists($result, 'error')) {
            throw new InvalidResponseException($result->error);
        }

        // Set the response
        $this->response = $result;

        return $this;
    }
}