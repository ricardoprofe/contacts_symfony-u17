<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    #[Route('/', name: 'app_main_page')]
    public function index(Request $request): Response
    {
        $form = $this->createFormBuilder(null)
            ->add('query', TextType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('Search', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted()){
            return $this->redirectToRoute('search_contact',
                ['search_string' => $form->getData()['query'] ]
            );
        }
        return $this->render('main_page/index.html.twig', [
            'page_title' => 'My Contacts App - Main page',
            'form' => $form,
        ]);
    }
}
