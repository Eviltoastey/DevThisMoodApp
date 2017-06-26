<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $date = new \DateTime();
        $todayEvents = [];
        $eventLimit = 0;

        $weatherService = $this->get('pianosolo.weather');
        $weather = $weatherService->getCityData('weather', 'Rotterdam', array('param' => ''));

        $advice = $this->getWeatherAdvice($weather->weather[0]->main, $weather->main->temp - 273.1);

        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository('AppBundle:Event')->findByUserId($this->getUser());

        if($eventLimit != 4){
            foreach ($events as $event){
                $eventLimit++;
                $startdate = $event->getStartDate();

                if($startdate->format('Y-m-d') == $date->format('Y-m-d')){
                    array_push($todayEvents, $event);
                }
            }
        }

        $date = new \DateTime();

        return $this->render('default/index.html.twig', [
            'weather' => $weather,
            'events' => $todayEvents,
            'advice' => $advice,
            'date' => $date,
        ]);
    }

    public function getWeatherAdvice($weather, $celsius) {

        $user = $this->getUser()->getUsername();

        switch ($weather) {
            case "Clouds":
                if($celsius < 18){
                    return "Afspraak inplannen om te gamen " . $user . " ? Het is geen lekker weer. ";
                } else {
                    return "Hey! Buiten chillen " . $user .  " ? Het is lekker weer.";
                }
                break;
            case "Clear":
                if($celsius < 18){
                    return "Afspraak inplannen om te wandelen " . $user . " ? Het is geen lekker weer. ";
                } else {
                    return "Lekker zwemmen " . $user .  " ? Het is super lekker weer.";
                }
                break;
            case "Rain":
                if($celsius < 18){
                    return "Afspraak inplannen om koffie te drinken " . $user . " ? Het regent. ";
                } else {
                    return "In de regen dansen " . $user .  " ? Het regent maar het is wel warm.";
                }
                break;
            case "Drizzle":
                if($celsius < 18){
                    return "Afspraak inplannen om koffie te drinken " . $user . " ? Het miezert. ";
                } else {
                    return "Wil je voetballen " . $user .  " ? Het miezert maar het is wel warm.";
                }
                break;
            case "Snow":
                    return "Sneeuwgevecht houden " . $user . " ? Het sneeuwt lekker door! ";
                break;
            case "Thunderstorm":
                    return "Het stormt buiten " . $user . ". Wil je even relaxen en de storm bekijken? ";
                break;
        }

    }
}
