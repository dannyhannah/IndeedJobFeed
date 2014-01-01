<?php

namespace Indeed\Feed\Response;

/**
 * Response interface
 *
 * @author Danny Hannah <danny@dannyhannah.co.uk>
 */
interface ResponseInterface
{
    public function getResponse();
    public function handleResponse($result);
}