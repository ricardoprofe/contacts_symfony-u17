<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Contact;
use App\Form\ContactType;

class ContactsController extends AbstractController
{
    #[Route('/contact/{id<\d+>}', name: 'single_contact')]
    public function contact(ManagerRegistry $doctrine, Request $request, $id=''): Response
    {
        $contact = $doctrine->getRepository(Contact::class)->find($id);
        if($id && !$contact){
            //If the id is incorrect
            $contact = new Contact();
            $contact->setId($id);
        }

        //Create the form
        $form = $this->createForm(ContactType::class, $contact);

        //Handle the request when the form has been submitted
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() ) {
            $entityManager = $doctrine->getManager();
            //Save button
            if($form->getClickedButton()->getName() === 'Save'){
                //Store the data
                $entityManager->persist($contact);
                $entityManager->flush();
                $action = "Contact updated";

                return $this->render('contacts/new_edit_contact.html.twig', [
                    'page_title' => 'My Contacts App - Contact updated',
                    'action' => $action,
                    'contact' => $contact
                ]);
            }

            //Delete button
            if($form->getClickedButton()->getName() === 'Delete') {
                //First, delete the contact's phones
                foreach($contact->getPhones() as $phone){
                    $entityManager->remove($phone);
                }
                //Delete the contact
                $entityManager->remove($contact);
                $entityManager->flush();
                $action = "Contact deleted";

                return $this->render('contacts/new_edit_contact.html.twig', [
                    'page_title' => 'My Contacts App - Contact',
                    'action' => $action,
                    'contact' => $contact
                ]);
            }

            //Add new phone
            if($form->getClickedButton()->getName() === 'Add_phone') {
                return $this->redirectToRoute('new_phone', [
                    'id' => $contact->getId(),
                ]);
            }

            //Delete phone
            if($form->getClickedButton()->getName() === 'Delete_phone') {
                return $this->redirectToRoute('phone_delete', [
                    'id' => $contact->getId(),
                    'number' => $form->getClickedButton()->getParent()['number']->getData(),
                ]);
            }

        }

        return $this->render('contacts/contact.html.twig', [
            'contact' => $contact,
            'page_title' => 'My Contacts App - Contact',
            'form' => $form->createView()
        ]);
    }


    #[Route('/contact_list', name: 'contact_list')]
    public function contactList(ManagerRegistry $doctrine): Response
    {
        return $this->render('contacts/list.html.twig', [
            'contacts' => $doctrine->getRepository(Contact::class)->findAll(),
            'page_title' => 'My Contacts App - Contact List'
        ]);
    }

    #[Route('/contact/search/{search_string}', name: 'search_contact')]
    public function searchContact(ManagerRegistry $doctrine, $search_string=''): Response
    {
        return $this->render('contacts/list.html.twig', [
            'contacts' => $doctrine->getRepository(Contact::class)->findByNameOrSurname($search_string),
            'page_title' => 'My Contacts App - Search results'
        ]);
    }

    #[Route('/contact/new/', name: 'new_contact')]
    public function newContact(ManagerRegistry $doctrine, Request $request): Response {
        $contact = new Contact();

        //Create the form
        $form = $this->createForm(ContactType::class, $contact);
        $form->remove('Delete');
        $form->remove('Add_phone');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() ) {
            if($form->getClickedButton()->getName() === 'Save') {
                $entityManager = $doctrine->getManager();
                $entityManager->persist($contact);
                $entityManager->flush();
                $action = 'New contact added';

                return $this->render('contacts/new_edit_contact.html.twig', [
                    'contact' => $contact,
                    'page_title' => 'My Contacts App - New contact',
                    'action' => $action
                ]);
            }

            //Add new phone
            if($form->getClickedButton()->getName() === 'Add_phone') {
                return $this->redirectToRoute('new_phone', [
                    'id' => $contact->getId(),
                ]);
            }

        }

        return $this->render('contacts/new_contact.html.twig', [
            'contact' => $contact,
            'page_title' => 'My Contacts App - New Contact',
            'form' => $form->createView()
        ]);
    }


}
