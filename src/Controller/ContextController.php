<?php

namespace App\Controller;

use App\Entity\Context;
use App\Form\ContextType;
use App\Repository\ContextRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ContextController extends AbstractController
{
    /**
     * @Route("/user/context", name="context")
     */
    public function index(ContextRepository $contextRepository, Request $request)
    {
        return $this->render('context/index.html.twig');
    }

    /**
     * @Route("/admin/context", name="context_create")
     * Zone de création des contextes pour l'administrateur
     */
    public function create(EntityManagerInterface $entityManager, ContextRepository $contextRepository, Request $request)
    {
        $context = new Context();

        $form=$this->createForm(ContextType::class,$context);
        $form->add('edit',SubmitType::class,
            ['label'=>'Créer']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form-> isValid()) {
            $context = $form->getData();
            $entityManager->persist($context);
            $entityManager->flush();
            return $this->redirectToRoute('context_create');
        }
        return $this ->render('context/create.html.twig',[
            'formContext' => $form->createView(),'contexts' =>$contextRepository->findAll(),
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="users_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')")
     * Récuperation de la liste des contextes
     */
    public function listContext(ContextRepository $contextRepository): Response
    {
        return $this->render('users/edit.html.twig', [
            'contexts' => $contextRepository->findAll(),
            var_dump($contextRepository),
        ]);
    }

    /**
     * @Route("/admin/context/{id}", name="context_delete", methods={"DELETE"})
     * *  @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, Context $context): Response
    {
        if ($this->isCsrfTokenValid('delete'.$context->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($context);
            $entityManager->flush();
        }

        return $this->redirectToRoute('context_create');
    }
}
