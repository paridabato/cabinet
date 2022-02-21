<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\WebApi;


/**
 * @Route("/cabinet")
 */
class CabinetController extends AbstractController{

    /**
     * @Route("/", name="cabinet")
     */

    public function index(Request $request){
        return $this->renderForm('cabinet/index.html.twig', [
            'form' => 'sdafsa',
        ]);
    }
}