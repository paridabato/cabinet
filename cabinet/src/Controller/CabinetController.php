<?php

namespace App\Controller;

use App\Form\Type\RegisterType;
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
    public function index(){
        return $this->renderForm('cabinet/index.html.twig');
    }

    /**
     * @Route("/profile", name="profile")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profile(Request $request){
        $ClientID = $request->getSession()->get('ClientID');
        $getClientData = WebApi::getInstance()->getClientData($ClientID, '');
        if (!str_starts_with($getClientData['PhoneNumber'], '+'))
            $getClientData['PhoneNumber'] = '+'.$getClientData['PhoneNumber'];

        $form = $this->createForm(RegisterType::class, $getClientData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $update_data['ClientID'] = $data['ID'];
            $update_data['FirstName'] = $data['FirstName'];
            $update_data['SecondName'] = $data['LastName'];
            $update_data['Adress'] = $data['Address'];
            $update_data['City'] = $data['City'];
            $update_data['Country'] = $data['IPCountry'];
            $update_data['PhoneNumber'] = $data['PhoneNumber'];
            $updateProfile = WebApi::getInstance()->updateProfile($update_data);

            if($updateProfile['ErrorCode'])
                $request->getSession()->getFlashBag()->add('danger', $updateProfile['ErrorDescription']);
            else $request->getSession()->getFlashBag()->add('success', 'success');
        }

        return $this->renderForm('cabinet/profile.html.twig', [
            'form' => $form,
        ]);
    }
}