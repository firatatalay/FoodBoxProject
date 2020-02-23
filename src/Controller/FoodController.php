<?php

namespace App\Controller;

use App\Entity\Food;
use App\Form\Food1Type;
use App\Form\FoodType;
use App\Repository\FoodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/food")
 */
class FoodController extends AbstractController
{
    /**
     * @Route("/", name="user_food_index", methods={"GET"})
     */
    public function index(FoodRepository $foodRepository): Response
    {
        $user = $this->getUser();

        return $this->render('food/index.html.twig', [
            'foods' => $foodRepository->findBy(['userid'=>$user->getid()]),
        ]);
    }

    /**
     * @Route("/new", name="user_food_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $food = new Food();
        $form = $this->createForm(Food1Type::class, $food);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();


            /* FILE UPLOAD START */
            /** @var file $file */
            $file = $form['image']->getData();
            if($file){
                $fileName = $this->generateUniqueFilename() . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'), //servis.yaml'de tanımlanan yol
                        $fileName
                    );
                }
                catch(fileException $e) {

                }
                $food->setImage($fileName);
            }
            /* FILE UPLOAD FINISH*/



            $user = $this->getUser();
            $food->setUserid($user->getid());
            $food->setStatus("New");

            $entityManager->persist($food);
            $entityManager->flush();

            return $this->redirectToRoute('user_food_index');
        }

        return $this->render('food/new.html.twig', [
            'food' => $food,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_food_show", methods={"GET"})
     */
    public function show(Food $food): Response
    {
        return $this->render('food/show.html.twig', [
            'food' => $food,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_food_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Food $food): Response
    {
        $form = $this->createForm(Food1Type::class, $food);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /* FILE UPLOAD START */
            /** @var file $file */
            $file = $form['image']->getData();
            if($file){
                $fileName = $this->generateUniqueFilename() . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'), //servis.yaml'de tanımlanan yol
                        $fileName
                    );
                }
                catch(fileException $e) {

                }
                $food->setImage($fileName);
            }
            /* FILE UPLOAD FINISH*/




            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_food_index');
        }

        return $this->render('food/edit.html.twig', [
            'food' => $food,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_food_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Food $food): Response
    {
        if ($this->isCsrfTokenValid('delete'.$food->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($food);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_food_index');
    }



    /**
     * @return string
     */
    private function generateUniqueFileName(){
        return md5(uniqid());
    }
}
