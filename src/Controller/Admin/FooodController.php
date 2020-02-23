<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Foood;
use App\Form\Admin\FooodType;
use App\Repository\Admin\FooodRepository;
use App\Repository\FoodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;

/**
 * @Route("/admin/foood")
 */
class FooodController extends AbstractController
{
    /**
     * @Route("/", name="admin_foood_index", methods={"GET"})
     */
    public function index(FooodRepository $fooodRepository): Response
    {
        return $this->render('admin/foood/index.html.twig', [
            'fooods' => $fooodRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="admin_foood_new", methods={"GET","POST"})
     */
    public function new(Request $request, $id, FoodRepository $foodRepository, FooodRepository $fooodRepository): Response
    {
        $fooods=$fooodRepository->findBy(['foodid' => $id]);
        $food=$foodRepository->findOneBy(['id'=>$id]);
        $foood = new Foood();
        $form = $this->createForm(FooodType::class, $foood);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            //*********upload file******>>>>>>>
            /** @var file $file */
            $file=$form['image']->getData();
            if($file){
                $fileName=$this->generateUniqueFileName() .'.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e){

                }
                $foood->setImage($fileName);
            }
            //*********upload file****************>


            $foood->setFoodid($id);
            $entityManager->persist($foood);
            $entityManager->flush();

            return $this->redirectToRoute('admin_foood_new', ['id'=>$id]);
        }

        return $this->render('admin/foood/new.html.twig', [
            'food' => $food,
            'foood' => $foood,
            'fooods' => $fooods,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_foood_show", methods={"GET"})
     */
    public function show(Foood $foood): Response
    {
        return $this->render('admin/foood/show.html.twig', [
            'foood' => $foood,
        ]);
    }

    /**
     * @Route("/{id}/edit/{hid}", name="admin_foood_edit", methods={"GET","POST"})
     */
    public function edit(Request $request,$hid, Foood $foood): Response
    {
        $form = $this->createForm(FooodType::class, $foood);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //*********upload file******>>>>>>>
            /** @var file $file */
            $file=$form['image']->getData();
            if($file){
                $fileName=$this->generateUniqueFileName() .'.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e){

                }
                $foood->setImage($fileName);
            }
            //*********upload file****************>

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_foood_new',['id'=>$hid]);
        }

        return $this->render('admin/foood/edit.html.twig', [
            'foood' => $foood,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/{hid}", name="admin_foood_delete", methods={"DELETE"})
     */
    public function delete(Request $request,$hid, Foood $foood): Response
    {
        if ($this->isCsrfTokenValid('delete'.$foood->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($foood);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_foood_new',['id'=>$hid]);
    }


    /**
     * @return string
     */
    private function generateUniqueFileName(){
        return md5(uniqid());
    }
}
