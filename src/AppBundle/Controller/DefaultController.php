<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $weatherService = $this->get('pianosolo.weather');
        $weather = $weatherService->getCityData('weather', 'Rotterdam', array('param' => ''));

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'weather' => $weather,
        ]);
    }
}
