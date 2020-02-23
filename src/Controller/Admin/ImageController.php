<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/image")
 */
class ImageController extends AbstractController
{
    /**
     * @Route("/", name="admin_image_index", methods={"GET"})
     */
    public function index(ImageRepository $imageRepository): Response
    {
        return $this->render('admin/image/index.html.twig', [
            'images' => $imageRepository->findAll(),
        ]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName(){
        return md5(uniqid());
    }


    /**
     * @Route("/new/{id}", name="admin_image_new", methods={"GET","POST"})
     */
    public function new(Request $request, $id, ImageRepository $imageRepository): Response
    {

        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
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
                $image->setImage($fileName);
            }
            /* FILE UPLOAD FINISH*/


            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('admin_image_new', ['id' => $id]);


        }



        return $this->render('admin/image/new.html.twig', [
            'image' => $image,
            'id' => $id,
            'images' => $imageRepository->findBy(['food'=>$id]),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_image_show", methods={"GET"})
     */
    public function show(Image $image): Response
    {
        return $this->render('admin_image/show.html.twig', [
            'image' => $image,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_image_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Image $image): Response
    {
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_image_index');
        }

        return $this->render('admin_image/edit.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/", name="admin_image_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Image $image): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_image_new', ['id' => $id]);
    }








}
