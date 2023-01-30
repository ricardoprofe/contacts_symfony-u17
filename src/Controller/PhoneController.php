<?php

namespace App\Controller;

use App\Form\PhoneType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Phone;
use App\Entity\Contact;

class PhoneController extends AbstractController
{
    #[Route('/phone/new/{id<\d+>}', name: 'new_phone')]
    public function newPhone(ManagerRegistry $doctrine, Request $request, $id=''): Response
    {
        $contact = $doctrine->getRepository(Contact::class)->find($id);
        if(!$contact) {
            return $this->render('phone/new_edit_phone.html.twig', [
                'contact'=> $contact,
                'phone' => null,
                'page_title' => 'My Contacts App - New phone',
                'action' => 'Failed to add phone: no contact found'
            ]);
        } else {
            $phone = new Phone();

            $form = $this->createForm(PhoneType::class, $phone);
            $form->add('Save', SubmitType::class);
            $form->remove('Delete_phone');

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $phone = $form->getData();
                $phone->setIdContact($contact);
                $entityManager = $doctrine->getManager();
                $entityManager->persist($phone);
                $entityManager->flush();

                return $this->redirectToRoute('single_contact', ['id' => $contact->getId()]);
            }

            return $this->render('phone/new_phone.html.twig', [
                'form' => $form->createView(),
                'contact' => $contact,
                'page_title' => 'My Contacts App - New phone',
            ]);
        }
    }


    #[Route('/phone/delete/{id<\d+>}/{number}', name: 'phone_delete')]
    public function deletePhone(ManagerRegistry $doctrine, $id='', $number=''): Response
    {
        $contact = $doctrine->getRepository(Contact::class)->find($id);
        if($contact == null) {
            return $this->render('phone/new_edit_phone.html.twig', [
                'contact'=> $contact,
                'phone' => null,
                'page_title' => 'My Contacts App - Delete phone',
                'action' => 'Failed to delete phone: no contact found'
            ]);
        } else {
            $entityManager = $doctrine->getManager();
            $phone = $doctrine->getRepository(Phone::class)->findOneBy(['number'=>$number, 'id_contact'=>$id]);
            if($phone) {
                $entityManager->remove($phone);
                $entityManager->flush();
                $action = "Phone deleted";
            } else {
                $action = "Failed to delete phone";
            }

            return $this->render('phone/new_edit_phone.html.twig', [
                'phone' => $phone,
                'contact' => $contact,
                'page_title' => 'My Contacts App - Delete phone',
                'action' => $action
            ]);
        }
    }


}
