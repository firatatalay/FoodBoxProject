<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Ordering;
use App\Form\Admin\OrderingType;
use App\Repository\Admin\OrderingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/ordering")
 */
class OrderingController extends AbstractController
{
    /**
     * @Route("/", name="admin_ordering_index", methods={"GET"})
     */
    public function index(OrderingRepository $orderingRepository): Response
    {
        return $this->render('admin/ordering/index.html.twig', [
            'orderings' => $orderingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_ordering_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ordering = new Ordering();
        $form = $this->createForm(OrderingType::class, $ordering);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ordering);
            $entityManager->flush();

            return $this->redirectToRoute('admin_ordering_index');
        }

        return $this->render('admin/ordering/new.html.twig', [
            'ordering' => $ordering,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_ordering_show", methods={"GET"})
     */
    public function show(Ordering $ordering): Response
    {
        return $this->render('admin/ordering/show.html.twig', [
            'ordering' => $ordering,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_ordering_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ordering $ordering): Response
    {
        $form = $this->createForm(OrderingType::class, $ordering);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_ordering_index');
        }

        return $this->render('admin/ordering/edit.html.twig', [
            'ordering' => $ordering,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_ordering_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ordering $ordering): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ordering->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ordering);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_ordering_index');
    }
}
