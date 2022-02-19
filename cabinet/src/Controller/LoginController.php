<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\LoginType;
use Symfony\Component\HttpFoundation\Request;
use App\WebApi;


class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request): Response
    {
        $form = $this->createForm(LoginType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data['IpAddress'] = $request->getClientIps();
            $getToken = WebApi::getInstance()->getToken($data);


            return $this->redirectToRoute('login');
        }

        return $this->renderForm('login/index.html.twig', [
            'form' => $form,
        ]);
    }
}
