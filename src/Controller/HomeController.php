<?php

namespace App\Controller;

use App\Entity\Admin\Messages;
use App\Entity\Food;
use App\Form\Admin\MessagesType;
use App\Repository\Admin\CommentRepository;
use App\Repository\Admin\FooodRepository;
use App\Repository\FoodRepository;
use App\Repository\ImageRepository;
use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Bridge\Google\Smtp\GmailTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SettingRepository $settingRepository, FoodRepository $foodRepository)
    {

        $setting=$settingRepository->findAll();
        $slider=$foodRepository->findBy(['status'=>'True'],[],6);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'setting' => $setting,
            'slider' => $slider,
        ]);
    }

    /**
     * @Route("/food/{id}", name="food_show", methods={"GET"})
     */
    public function show(Food $food, $id, ImageRepository $imageRepository, FoodRepository $foodRepository, CommentRepository $commentRepository, FooodRepository $fooodRepository): Response
    {

        $images=$imageRepository->findBy(['food' => '$id']);
        $slider=$foodRepository->findBy(['status'=>'True'],[],6);
        $comments=$commentRepository->findBy(['foodid'=>$id, 'status'=>'True']); // SADECE TRUE OLAN YORUMLARI GÖSTERİYOR
        $fooods=$fooodRepository->findBy(['foodid'=>$id,'status'=>'True']); // TRUE OLAN RESTAURANTLAR



        return $this->render('home/foodshow.html.twig', [
            'food' => $food,
            'images'=>$images,
            'slider' => $slider,
            'comments' => $comments,
            'fooods' => $fooods,

        ]);
    }

    /**
     * @Route("/about", name="home_about", methods={"GET"})
     */
    public function about(SettingRepository $settingRepository): Response
    {
        $setting=$settingRepository->findAll();

        return $this->render('home/aboutus.html.twig', [
            'setting' => $setting,
        ]);
    }

    /**
     * @Route("/menu", name="home_menu", methods={"GET"})
     */
    public function menu(SettingRepository $settingRepository,  FoodRepository $foodRepository): Response
    {
        $setting=$settingRepository->findAll();
        $foods=$foodRepository->findBy([],['title' => 'DESC'],6);

        return $this->render('home/menu.html.twig', [
            'setting' => $setting,
            'foods' => $foods,
        ]);
    }


    /**
     * @Route("/contact", name="home_contact", methods={"GET","POST"})
     */
    public function contact(SettingRepository $settingRepository,Request $request): Response
    {
        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);
        $submittedToken =$request->request->get('token');

        if ($form->isSubmitted()) {
            if ($this->isCsrfTokenValid('form-message', $submittedToken)) {
                $entityManager = $this->getDoctrine()->getManager();
                $message->setStatus('New');
                $message->setIp($_SERVER['REMOTE_ADDR']);
                $entityManager->persist($message);
                $entityManager->flush();
                $this->addFlash('success','Your message has been sent successfully');
                //********* SEND EMAIL **************//
                //  $email = (new Email())
                //    ->from($setting[0]->getSmtpemail())
                //  ->to($form['email']->getData())
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                // ->subject('All resturant Your request!')
                // ->text('Sending emails is fun again!')
                // ->html("Dear ".$form['email']->getData() ."<br>
                //    <p>We will evaluate your request and contact you as soon as possible</p>
                //   Thank You <br>
                //   ===============================================
                //  <br>".$setting[0]->getCompany()." <br>
                //  Address : ".$setting[0]->getAddress()."<br>
                //  Phone : ".$setting[0]->getphone()."<br>"
                // );
                // $transport = new GmailTransport($setting[0]->getSmtpemail(),$setting[0]->getSmtppassword());
                // $mailer = new Mailer($transport);
                // $mailer = send($email);
                //<<<<<<<<<<<<<<<<< SEND EMAIL >>>>>>>>>>>>>>>>>>>>>>>>//

                return $this->redirectToRoute('home_contact');
            }
        }
        $setting=$settingRepository->findAll();
        return $this->render('home/contactus.html.twig', [
            'setting' => $setting,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/message", name="messages_new", methods={"GET","POST"})
     */
    public function message(Request $request): Response
    {
        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('home/contactus/new.html.twig');
        }

        return $this->render('home/contactus/new.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }








}
