<?php

namespace Indeed\Feed\Request;

use Indeed\Feed\Response\ResponseInterface;

/**
 * Request interface
 *
 * @author Danny Hannah <danny@dannyhannah.co.uk>
 */
interface RequestInterface
{
    public function setResponse(ResponseInterface $response);
    public function setUrl($url);
    public function setOptions(array $options);
    public function sendRequest();
}