<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Employer;
use App\Repository\EmployerRepository;

use Symfony\Component\HttpFoundation\Request ;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Entity\Service;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\EmployerType;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(EmployerRepository $repo)
    {
       
        $employers=$repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'employers'=>$employers
        ]);
    }
    /**
     * @Route("/",name="home")
     */
    public function home(){
        return $this->render('blog/home.html.twig');
    }

      /**
     * @Route("/blog/new",name="blog_create")
     * @Route("/blog/{id}/edit",name="blog_edit")
     */
    public function form(Employer $employer=null, Request $request,ObjectManager $manager){
        if (!$employer) {
            $employer=new Employer;
        }
       
       
         $form = $this->createFormBuilder($employer)
                         ->add('matricule')
                         ->add('nomcomplet')
                         ->add('datenaiss', DateType::class, [
                             'widget' => 'single_text',
                             'format' => 'yyyy-MM-dd',])
                         ->add('salaire')
                        ->add('service',EntityType::class,['class'=>Service::class,'choice_label'=>'libelle'])
                        
                         ->getForm();

     //   $form=$this->createForm(EmployerType::class, $employer);
                        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
        if (!$employer) {
              $employer->setdatenaiss(new\dateTime());
          }
            $manager->persist($employer);
            $manager->flush();
         //   return $this->redirectToRoute('blog_show',['id'=>$employer->getId()]);
        }
        return $this->render('blog/create.html.twig',['formEmployer'=>$form->createView(),
        'editMode'=>$employer->getId()!==null] );
    }
    /**
     * @Route("/blog/{id}",name="blog_show")
     */
    public function show(Employer $employer){
      
        
        return $this->render('blog/show.html.twig',['employer'=>$employer]);
    }
    /**
     * @Route("/blog/{id}/delete",name="blog_delete")
     */
    public function delete(Employer $employer,ObjectManager $manager){
        $manager->remove($employer);
        $manager->flush();
        return $this->redirectToRoute('blog');

    }
  
}
