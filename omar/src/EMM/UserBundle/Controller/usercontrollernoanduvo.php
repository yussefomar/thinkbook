
<?php

namespace EMM\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use EMM\UserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use EMM\UserBundle\Entity\User;

class UserController extends Controller {

    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        /* $users = $em->getRepository('EMMUserBundle:User')->findAll();
          $res = 'lista de usuarios:<br />'; */
        $dql = "SELECT u FROM EMMUserBundle:User u ORDER BY u.id DESC";
        $users = $em->createQuery($dql); /* hasta aca hicimos la consulta */
        /* vamos paginar */
        $paginator = $this->get('knp_paginator');
        
        $pagination = $paginator->paginate(
                $users, $request->query->getInt('page', 2), 3); /* primer campo consulta almacenada que esta en users ,despues el objeto request metodo query y getind y dentro de eso page, con un uno, el numero de pagina que se va */
        /* a inciar que va a hacer uno, tambien hay que ponerl el limite de paginas a la vista vamos amostrar 3                 */
           $deleteFormAjax=$this->createCustomForm(':USER_ID','DELETE','emm_user_delete'); /*:user_id lo utlice comocomidin para que despues con jquery le de un valor*/
           
          // if(!empty($users)){
            //   print_r($users);
           //}
          // foreach ($users as $user ){
            //   print_r($user->id);
          //     exit();
         //  }
           
           
           
        return $this->render('EMMUserBundle:User:index.html.twig', array('pagination' => $pagination,'delete_form_ajax'=>$deleteFormAjax->createView()));
    }
    
    public function indedxAction(Request $request)
    {
       /// $em = $this->getDoctrine()->getManager();
        
         $users = $em->getRepository('EMMUserBundle:User')->findAll();
        
        
        
        $res = 'Lista de usuarios: <br />';
        
        foreach($users as $user)
        {
            $res .= 'Usuario: ' . $user->getUsername() . ' - Email: ' . $user->getEmail() . '<br />';
        }
        
        return new Response($res);
        
        
      ///  $dql = "SELECT u FROM EMMUserBundle:User u ORDER BY u.id DESC";
      //  $users = $em->createQuery($dql);
        
      ///  $paginator = $this->get('knp_paginator');
       // $pagination = $paginator->paginate(
      //      $users, $request->query->getInt('page', 1),
      //      10
      //  );
        
      //  $deleteFormAjax = $this->createCustomForm(':USER_ID', 'DELETE', 'emm_user_delete');
        
        //return $this->render('EMMUserBundle:User:index.html.twig', array('pagination' => $pagination, 'delete_form_ajax' => $deleteFormAjax->createView()));
    }
     private function createCustomForm($id, $method, $route)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod($method)
            ->getForm();
    }
    public function viewAction($id) {
        $repository = $this->getDoctrine()->getRepository('EMMUserBundle:User');
        $user = $repository->find($id);
        if (!$user) {
            $messageException = $this->get('translator')->trans('User not found.');
            throw $this->createNotFoundException(); /* nos va a mandar a una pagina 404 */
        }
        $deleteForm=$this->createCustomForm($user->getId(),'DELETE','emm_user_delete');//usame delteleform acapero como cretae cusmot es reutizable
        return $this->render('EMMUserBundle:User:view.html.twig', array('user' => $user,'delete_form'=>$deleteForm->createView()));
    }

    public function addAction() {
        $user = new User();
        $form = $this->createCreateForm($user);
        return $this->render('EMMUserBundle:User:add.html.twig', array('form' => $form->createView()));
    }

    private function createCreateForm(User $entity) {
        $form = $this->createForm(new UserType(), $entity, array('action' => $this->generateUrl('emm_user_create'), 'method' => 'POST')); /* el ultimo campo es la accion dond ese va redirigir el formuladrio */

        return $form;
    }

    public function createAction(Request $request) {
        $user = new User();
        $form = $this->createCreateForm($user); /* porque necesitamos obtener nuestro forumalario que estamos renderisando */
        $form->handleRequest($request); /* el cual dentro de este llamamos a la peticion de nuetro formulario, y lo procesamos con el objeto request */
        if ($form->isValid()) {
            $password = $form->get('password')->getData(); /* con esto estamosel recuperando que estamos almancenando dentro de nuestro formulario y guardando en la variable password */

            $passwordContraint = new Assert\NotBlank(); /* llama al contraint notBlak */
            $errorList = $this->get('validator')->validate($password, $passwordContraint); /* para configuar nuestro herror, dentro vamso a colocar el passowerd y seguido nuestra reglas de validacion */

            if (count($errorList) == 0)/* si no existe error alguno entonces nosotros vamos a procesar todo lo que corresponde a al persistencia */ {
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $password); /* le vamos amandar al user que corresponde a nuestra entidad y al password que acabamos de ingresar */
                $user->setPassword($encoded); /* finalmente lo setiamos */
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $succesMessage = $this->get('translator')->trans('The user has been created');

                $this->addFlash('mensaje', $succesMessage)/* primero elnombre del mensaje y seguido del lmensaje en concreto qeu es enviado a la vista en dodne se mostrara */;
                return $this->redirectToRoute('emm_user_index');
            } else {
                $errorMessage = new FormError($errorList[0]->getMessage()); /* que no nos permita ingresar un campo vacio */
                $form->get('password')->addError($errorMessage); /* seguido del mensaje, vamos arecuperar el correcto mensaje de error */
            }
        }
        return $this->render('EMMUserBundle:User:add.html.twig', array('form' => $form->createView())); /* si existe algun problema entonces renderizamos dneuevo */
    }

    public function editAction($id) {

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('EMMUserBundle:User')->find($id);
        if (!$user) {
            $messageException = $this->get('translator')->trans('User not found.');
            throw $this->createNotFoundException(); /* nos va a mandar a una pagina 404 */
        }
        $form = $this->createEditForm($user);
        return $this->render('EMMUserBundle:User:edit.html.twig', array('user' => $user, 'form' => $form->createView()));
    }

    private function createEditForm(User $entity) {
        $form = $this->createForm(new UserType(), $entity, array('action' => $this->generateUrl('emm_user_update', array('id' => $entity->getId())), 'method' => 'PUT'));
        return $form;
    }

    public function updateAction($id, Request $request) {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('EMMUserBundle:User')->find($id);
        if (!$user) {
            $messageException = $this->get('translator')->trans('User not found.');
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createEditForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData(); /* recuperamos el password del formulario */
            if (!empty($password)) {
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $password);
                $user->setPassword($encoded);
            } else {
                $recoverPass = $this->recoverPass($id);
                $user->setPassword($recoverPass[0]['password']);
            }
            if ($form->get('role')->getData() == 'ROLE_ADMIN') {
                $user->setIsActive(1);
            }
            $em->flush();
            $messageException = $this->get('translator')->trans('The user has been modified.');
            $this->addFlash('mensaje', $messageException);
            return $this->redirectToRoute('emm_user_edit', array('id' => $user->getId()));
            ;
        }

        return $this->render('EMMUserBundle:User:edit.html.twig', array('user' => $user, 'form' => $form->createView()));
    }

    private function recoverPass($id) {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
                        'SELECT u.password FROM EMMUserBundle:User u WHERE u.id=:id'
                )->setParameter('id', $id);

        $currentPass = $query->getResult(); /* recuperao el resultado */
        return $currentPass;
    }
         
    private function createDeleteForm($user)
    {
                       return $this->createFormBuilder()
            ->setAction($this->generateUrl('emm_user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
    
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('EMMUserBundle:User')->find($id);
        
        if(!$user)
        {
            $messageException = $this->get('translator')->trans('User not found.');
            throw $this->createNotFoundException($messageException);
        }
        
        $allUsers = $em->getRepository('EMMUserBundle:User')->findAll();
        $countUsers = count($allUsers);
        
        // $form = $this->createDeleteForm($user);
        $form = $this->createCustomForm($user->getId(), 'DELETE', 'emm_user_delete');
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            if($request->isXMLHttpRequest())
            {
                $res = $this->deleteUser($user->getRole(), $em, $user);
                
                return new Response(
                    json_encode(array('removed' => $res['removed'], 'message' => $res['message'], 'countUsers' => $countUsers)),
                    200,
                    array('Content-Type' => 'application/json')
                );
            }
            
            $res = $this->deleteUser($user->getRole(), $em, $user);
            $this->addFlash($res['alert'], $res['message']);
            return $this->redirectToRoute('emm_user_index');            
        }
    }
    
    private function deleteUser($role, $em, $user)
    {
        if($role == 'ROLE_USER')
        {
            $em->remove($user);
            $em->flush();
            
            $message = $this->get('translator')->trans('The user has been deleted.');
            $removed = 1;
            $alert = 'mensaje';
        }
        elseif($role == 'ROLE_ADMIN')
        {
            $message = $this->get('translator')->trans('The user could not be deleted.');
            $removed = 0;
            $alert = 'error';
        }
        
        return array('removed' => $removed, 'message' => $message, 'alert' => $alert);
    }
    
     /*public function deleteAction(Request $request ,$id){ //ahora tiene que servir tanto para nuestras peticiones en ajz y para neustra vistas en view
        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository('EMMUserBundle:User')->find($id);
        if (!$user) {
            $messageException = $this->get('translator')->trans('User not found.');
            throw $this->createNotFoundException($messageException);
        }
        $allUsers=$em->getRepository('EMMUserBundle:User')->findAll();
        $countUsers=count($allUsers);//metodo de php para contar
        //$form=$this->createDeleteForm($user); vamos a estructurarlo de otra forma
        $form=$this->createCustomForm($user->getId(),'DELETE','emm_user_delete');
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid())
        {
            
            if($request->isXMLHttpRequest())//enn este if todo lo relacionado con ajax
            {
                $res=$this->deleteUser($user->getRole(),$em,$user);//es por el rol , y es un arreglo
                return new Response(
                json_encode(array('removed'=>$res['removed'],'message'=>$res['message'],'countUsers'=>$countUsers)),200,array('Content-Type'=>'application/json')//estamos recorriendo el arreglo, 200 es porque es el estado de nuestra respuesta, y ademas el tipo de estado json ya que es tipo jason
                        );//retornar el objeto response
            }
            $res=$this->deleteUser($user->getRole(), $em, $user);
            $this->addFlash($res['alert'], $res['message']);
            return $this->redirectToRoute('emm_user_index');
        }
    }
    
    private function deleteUser($role,$em,$user){
        if($role=='ROLE_USER'){
            $em->remove($user);
            $em->flush();
            
            $message=$this->get('translator')->trans('The user has been deleted.');
            $removed=1;//1 para saber que fue eliminado
            $alert='mensaje';
        }
        elseif($role=='ROLE_ADMIN'){
         $message=$this->get('translator')->trans('The user could not be deleted.');
         $removed=0;
         $alert='error';
             
        }
        return array('removed'=>$removed,'message'=>message,'alert'=>$alert);//para reutilizarlo 
    }
      
      */
    
}
