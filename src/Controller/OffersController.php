<?php

namespace App\Controller;
use App\Data\SearchData;
use App\Entity\Jobs;
use App\Entity\Candidates;
use App\Repository\CandidatesRepository;
use App\Repository\JobsRepository;
use App\Form\SearchForm;
use App\Form\JobsAddType;
use App\Form\SearchFormCandidate;
use App\Data\SearchDataCandidate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class OffersController extends AbstractController
{
     private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    /**
     * @Route("/dashboard/offres", name="dashboard_offers")
     */
     public function index(JobsRepository $repository , Request $request ){ 
        $data=new SearchData();
         $data->page = $request->query->getInt('page', 1);
        $form= $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request) ;
        $jobs = $repository->findSearch($data);
     
       
   foreach ($jobs as $job){
       $bb=$job->getCreatedAt()->format('Y-m-d');
        $expired=$job->getExpiredAt()->format('Y-m-d');
           $datexpire= $job->getExpiredAt();
           $ccc= $job->getCreatedAt();
           $title=$job->getTitle();
            $str = [' ','é','è','\'','ç'];
            $rplc =['-','e','e','','c'];
             $candidates=[];
            foreach($job->getApplications() as $candidate)
            {
             $candidates[]=[
             'id'=>$candidate->getId() ,
             'applicant'=>$candidate->getCandidate()->getfname(),
             

             ] ;

            }

            $jobsArray[] = [

                   'id' => $job->getId(),
                'title' => $job->getTitle(),
                'image' => $job->getImage(),
                'cover' => $job->getCover(),
              'country' => strtolower($job->getCountry()->getName()),
                'city' => $job->getCity(),
                'beginAt'=>$job->getCreatedAt(),
                'expireAt' => $job->getExpiredAt(),
                'type' => $job->getTypeid()->getTitle(),
                'exp' => $job->getExp()->getTitle() ,
                'presentation' => $job->getPresentation(),
                'resp' => $job->getResp() ,
                'req' => $job->getReq (),
                'today'=>strtotime(date('Y/m/d')),
               'applications'=>$job->getApplications(),
               'applicant'=> $candidates,
                 'slug'=> strtolower(str_replace($str,$rplc,$title)),
                'expire'=>strtotime($expired),
                 'time'=>strtotime($bb),
               

 
            ];
        }
     
         
         if(empty($jobsArray)){$jobsArray=[];}
      
   
        return $this->render('dashboard/offres/offers.html.twig', [
            'jobs' => $jobsArray  ,'form' => $form-> createView()
        ]);
    }
 
  

     /**
     * @Route("/dashboard/offres/Ajouter", name="dashboard_offers_add")
     * @param Request $request
     * @return Response
     * 
     */
    public function new( Request $request, EntityManagerInterface $em)
    {
        $jobs = new Jobs();
        $form = $this->createForm(JobsAddType::class, $jobs);
        $form->handleRequest($request);

          if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

           
    if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedImage = $form['imagefield']->getData();
            $uploadedCover = $form['coverfield']->getData();

           
            $destination = $this->getParameter('kernel.project_dir').'/public/images/jobs';
            if ($uploadedImage) {
            $ImageName = 'image'.uniqid().'.'.$uploadedImage->guessExtension();
            $CoverName = 'cover'.uniqid().'.'.$uploadedCover->guessExtension();
                $newImageName = $ImageName;
                $uploadedImage->move($destination,$newImageName);
                $jobs->setImage($ImageName);
                
            }
            else { $jobs->setImage('DefaultImage.png');}
             if ($uploadedCover) {
               
              
                $newCoverName = $CoverName;
                $uploadedCover->move($destination,$newCoverName);
                $jobs->setCover($CoverName);
           
            }

            else { $jobs->setCover('DefaultCover.png');}
            $jobs->setStatus('Actif');
        }
          
         $em->persist($jobs);
            $em->flush();
             $this->addFlash('success', 'L\'offre d\'emploi a été Ajoutée avec succès!');
            return $this->redirectToRoute('dashboard_offers');

        }
        return $this->render('dashboard/offres/Ajouter.html.twig', [
            "form" => $form->createView()
        ]);
    }



      /**
     * @Route("/dashboard/offres/modifier/{id}", name="offers_edit")
     * @param Request $request
     * @return Response
     */
    public function edit(Jobs $job, Request $request): Response
    {
        $jobs = new Jobs();
        $form = $this->createForm(JobsAddType::class, $job);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

           
    if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedImage = $form['imagefield']->getData();
            $uploadedCover = $form['coverfield']->getData();

           
            $destination = $this->getParameter('kernel.project_dir').'/public/images/jobs';
            if ($uploadedImage) {
            $ImageName = uniqid().'.'.$uploadedImage->guessExtension();
            $CoverName = uniqid().'.'.$uploadedCover->guessExtension();
                $newImageName = $ImageName;
                $uploadedImage->move($destination,$newImageName);
                $job->setImage($ImageName);
               
               
               
            }
             if ($uploadedCover) {
               
              
                $newCoverName = $CoverName;
                $uploadedCover->move($destination,$newCoverName);
                $job->setCover($CoverName);
               
               
            }

        }
         $em->persist($job);
            $em->flush();
             $this->addFlash('success', 'L\'offre d\'emploi a été modifié avec succès!');
            return $this->redirectToRoute('dashboard_offers');

        }
        return $this->render('dashboard/offres/modifier.html.twig', [
            "form" => $form->createView()
        ]);
    }


 /**
     * @Route("/dashboard/offres/supprimer{id}", name="offre_delete")
     * @param Jobs $jobs
     * @return RedirectResponse
     */
    public function delete(Jobs $jobs): RedirectResponse
    {
       
   
            $em = $this->getDoctrine()->getManager();
          $jobs->setStatus('nonActif');
          $em->persist($jobs);
          $em->flush();
        
        return $this->redirectToRoute("dashboard_offers");
    }


     /**
     * @Route("dashboard/offres/{id}", name="postulants")
    *
     */
     public function details(Jobs $jobs , Request $request )
    {
   
      $data=new SearchDataCandidate();
       
        $form= $this->createForm(SearchFormCandidate::class, $data);
        $form->handleRequest($request) ;
        $candidates=$this->manager->getRepository(Candidates::class)->findSearchCandidate($data);
              $jobid= $jobs->getId();
        foreach ($candidates as $candidate){
            
        
             $offres=[];
            foreach($candidate->getApplications() as $offre)
            {
             $title=$offre->getJob()->getTitle();
             $cc=$offre->getJob()->getId();
             $offres[]=[
             'id'=>$offre->getId() ,
             'jobid'=>$offre->getJob()->getId(),
             'title'=>$offre->getJob()->getTitle(),
           

             ] ;

          
  
          if ($jobid==$cc) {
            $candidatesArray[] = [
               $id=$candidate->getId(),
               'id'=>$candidate->getId(),
              'fname'=>$candidate->getFname(),
              'lname'=>$candidate->getLname(),
              'seen'=>$candidate->getSeen(),
               'offre'=>$cc,
              'titre' => $candidate->getTitre()->getTitle(),

             
            ];
}   
        }
             
            foreach($jobs->getApplications() as $candidate)
              
            { $jobcandidat=$candidate->getJob()->getId();
                if ($jobid==$jobcandidat) {
             $candidatess[]=[
             'id'=>$candidate->getId() ,
             'fname'=>$candidate->getCandidate()->getfname(),
             'lname'=>$candidate->getCandidate()->getlname(),
             'seen'=>$candidate->getCandidate()->getSeen(),
              'titre'=>$candidate->getCandidate()->getTitre()->getTitle(),
             'jj'=>$candidate->getJob()->getId(),
             

             ] ;}
           
          }
            }
               if(empty($candidatesArray)){$candidatesArray=[];}

         
 
         
        return $this->render('dashboard/offres/postulants.html.twig',['form' => $form-> createView(),'candidates'=>$candidatesArray]);
    }



}
