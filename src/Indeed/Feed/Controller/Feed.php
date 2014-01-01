<?php

namespace Indeed\Feed\Controller;

use Indeed\Feed\Request\Request;
use Indeed\Feed\Response\Response;

class Feed
{
    protected $twig;
    protected $messages = array();

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function defaultAction()
    {
        echo $this->twig->render('feed.html', array(
            'results' => array(),
            'messages' => $this->messages
        ));
    }

    public function getFeed()
    {
        $data = $_GET;
        if (!isset($data['search'])) {
            return $this->deafaultAction();
        }

        if (!isset($data['search']['location']) || 
            !isset($data['search']['keywords']) ||
            empty($data['search']['location'])  || 
            empty($data['search']['keywords'])
        ) {
            $this->messages['warning'][] = 'Sorry, please enter both a location and a keyword';
            return $this->defaultAction();
        }

        $request = new Request;
        $response = new Response;

        $options = array(
            'publisher' => PUBLISHER_KEY,
            'q'         => $data['search']['keywords'],
            'l'         => $data['search']['location'],
            'co'        => 'gb',
            'v'         => 2,
            'limit'     => 9999
        );

        $url = 'http://api.indeed.com/ads/apisearch?';

        $request->setResponse($response);
        $request->setOptions($options);
        $request->setUrl($url);
        
        $response = $request->sendRequest();

        echo $this->twig->render('feed.html', array(
            'results' => $response->getResponse()->results->result,
            'location' => $data['search']['location'],
            'keywords' => $data['search']['keywords'],
            'messages' => $this->messages
        ));
    }
}