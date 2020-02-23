<?php

namespace App\Controller;

use App\Entity\Admin\Comment;
use App\Entity\Admin\Ordering;
use App\Entity\User;
use App\Form\Admin\CommentType;
use App\Form\Admin\OrderingType;
use App\Form\UserType;
use App\Repository\Admin\CommentRepository;
use App\Repository\Admin\FooodRepository;
use App\Repository\Admin\OrderingRepository;
use App\Repository\FoodRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Tests\Foo;


/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('user/show.html.twig');
    }


    /**
     * @Route("/comments", name="user_comments", methods={"GET"})
     */
    public function comments(CommentRepository $commentRepository): Response
    {
        $user=$this->getUser();
        $comments=$commentRepository->getAllCommentsUser($user->getId());

        return $this->render('user/comments.html.twig', [
            'comments' => $comments,
            ]);
    }

    /**
     * @Route("/orders", name="user_orders", methods={"GET"})
     */
    public function orders(OrderingRepository $orderingRepository): Response
    {
        $user=$this->getUser();
        $orderings=$orderingRepository->getAllOrderingsUser($user->getId());
//        dump($orderings);
//        die();
        return $this->render('user/orders.html.twig',[
            'orderings' => $orderings,
        ]);
    }





    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
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
                $user->setImage($fileName);
            }
            /* FILE UPLOAD FINISH*/

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request,$id, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        if($user ->getId() != $id)
        {
            echo "Wrong User!";
            die();
        }
        $form = $this->createForm(UserType::class, $user);
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
                $user->setImage($fileName);
            }
            /* FILE UPLOAD FINISH*/


            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }


    /**
     * @return string
     */
    private function generateUniqueFileName(){
        return md5(uniqid());
    }



    /**
     * @Route("/newcomment/{id}", name="user_new_comment", methods={"GET","POST"})
     */
    public function newcomment(Request $request,$id): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $submittedToken =$request->request->get('token');
        if ($form->isSubmitted()) {
            if ($this->isCsrfTokenValid('comment', $submittedToken)) {
                $entityManager = $this->getDoctrine()->getManager();
                $comment->setStatus('New');
                $comment->setIp($_SERVER['REMOTE_ADDR']);
                $comment->setfoodid($id);
                $user=$this->getUser();
                $comment->setUserid($user->getId());
                $entityManager->persist($comment);
                $entityManager->flush();
                $this->addFlash('success', 'Your message has been sent successfuly.');
                return $this->redirectToRoute('food_show', ['id' => $id]);
            }
        }

        return $this->redirectToRoute('food_show',['id'=>$id] );
    }

//    /**
//     * @Route("/order/{rid}/{fid}", name="user_order_new", methods={"GET","POST"})
//     */
//    public function neworder(Request $request): Response
//    {
//        $order = new Order();
//        $form = $this->createForm(OrderingType::class, $order);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($order);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('user_order_index');
//        }
//
//        return $this->render('user/neworder.html.twig', [
//            'order' => $order,
//            'form' => $form->createView(),
//        ]);
//    }


    /**
     * @Route("/new/{rid}/{fid}", name="user_ordering_new", methods={"GET","POST"})
     */
    public function neworder(Request $request, $rid, $fid, FoodRepository $foodRepository, FooodRepository $fooodRepository): Response
    {

       $quantity=$_REQUEST["quantity"];
//        dump($quantity);
//        die();
        $food=$foodRepository->findOneBy(['id'=>$rid]);
        $foood=$fooodRepository->findOneBy(['id'=>$fid]);
        $total=$quantity * $foood->getPrice();
//        dump($total);
//        die();
        $ordering = new Ordering();
        $form = $this->createForm(OrderingType::class, $ordering);
        $form->handleRequest($request);
        $submittedToken =$request->request->get('token');
        if ($form->isSubmitted()) {
            if ($this->isCsrfTokenValid('form-order', $submittedToken)) {
                $entityManager = $this->getDoctrine()->getManager();
                $ordering->setStatus('New');
                $ordering->setIp($_SERVER['REMOTE_ADDR']);
                $ordering->setFoodid($rid);
                $ordering->setFooodid($fid);
                $user=$this->getUser();
                $ordering->setUserid($user->getid());
                $ordering->setQuantity($quantity);
                $ordering->setTotal($total);
                $ordering->setCreatedAt(new \DateTime());
                $entityManager->persist($ordering);
                $entityManager->flush();

                return $this->redirectToRoute('user_orders');
            }
        }

        return $this->render('user/neworder.html.twig', [
            'ordering' => $ordering,
            'foood'=>$foood,
            'food'=>$food,
            'total'=>$total,
            'quantity'=>$quantity,
            'form' => $form->createView(),
        ]);
    }






}
