<?php

namespace EMM\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    public function loginAction(){
        
        
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
        $authenticationUtils=$this->get('security.authentication_utils');
        $error=$authenticationUtils->getLastAuthenticationError();
        $lastUsername=$authenticationUtils->getLastUsername();
        
        return $this->render('EMMUserBundle:Security:login.html.twig',array('lastUsername'=>$lastUsername,'error'=>$error));
        }else{
            
            return $this->redirectToRoute('emm_task_custom');
            
        }
       
        
    }
    
    public function loginCheckAction(){
        
    }
    
}
