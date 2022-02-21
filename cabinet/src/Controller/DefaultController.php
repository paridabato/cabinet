<?php

namespace App\Controller;

use App\Form\Type\RegisterType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\LoginType;
use Symfony\Component\HttpFoundation\Request;
use App\WebApi;


class DefaultController extends AbstractController{

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request): Response
    {
        $ClientID = $request->getSession()->get('ClientID');

        if(is_null($ClientID))
            return $this->redirectToRoute('login');

        return $this->redirectToRoute('cabinet');
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request): Response
    {
        $form = $this->createForm(LoginType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data['IpAddress'] = $request->getClientIps()[0];
            $getToken = WebApi::getInstance()->getToken($data);

            if($getToken['ErrorCode']){
                $request->getSession()->getFlashBag()->add('danger', $getToken['ErrorDescription']);
                return $this->redirectToRoute('login');
            }
            $request->getSession()->set('ClientID', $getToken['ClientID']);
            $request->getSession()->set('ClientEmail', $getToken['ClientEmail']);

            return $this->redirectToRoute('cabinet');
        }

        return $this->renderForm('login/index.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/register", name="register")
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function register(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(RegisterType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $checkEmail = WebApi::getInstance()->checkEmail(['Email' => $data['Email'], 'CompanyID' => 2]);
            $checkPhone = WebApi::getInstance()->checkPhone(['Phone' => $data['PhoneNumber'], 'CompanyID' => 2]);

            $check = true;
            if($checkEmail['IsRegistered']){
                $request->getSession()->getFlashBag()->add('danger', 'Email IsRegistered');
                $check = false;
            }
            if($checkPhone['IsRegistered']){
                $request->getSession()->getFlashBag()->add('danger', 'Phone IsRegistered');
                $check = false;
            }
            if(!$check)
                return $this->redirectToRoute('register');

            $addClient = WebApi::getInstance()->addClient($data);

            if($addClient['ErrorCode']){
                $request->getSession()->getFlashBag()->add('danger', $addClient['ErrorDescription']);
                return $this->redirectToRoute('register');
            }

            $email = (new TemplatedEmail())
                ->from(new Address('DoNotReply@ConfirmCode.com', 'ConfirmCode'))
                ->to($data['Email'])
                ->subject('ConfirmCode')
                ->htmlTemplate('emails/ConfirmCode.html.twig')
                ->context([
                    'ConfirmCode' => $addClient['ConfirmCode']
                ])
            ;
            $mailer->send($email);

            $request->getSession()->getFlashBag()->add('success', 'success');

            return $this->redirectToRoute('login');
        }

        return $this->renderForm('register/index.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/confirm", name="confirm")
     */
    public function confirm(Request $request){
        $ConfirmCode = $request->query->get('ConfirmCode');
        $confirmClient = WebApi::getInstance()->confirmClient(['ConfirmCode' => $ConfirmCode]);

        if($confirmClient['ErrorCode'])
            $request->getSession()->getFlashBag()->add('danger', $confirmClient['ErrorDescription']);
        else
            $request->getSession()->getFlashBag()->add('success', 'success');

        return $this->redirectToRoute('login');
    }
}