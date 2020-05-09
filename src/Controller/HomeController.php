<?php

namespace App\Controller;

use App\Entity\Admin\Messages;
use App\Entity\House;
use App\Entity\Setting;
use App\Form\Admin\MessagesType;
use App\Repository\Admin\CommentRepository;
use App\Repository\HouseRepository;
use App\Repository\ImageRepository;
use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Bridge\Google\Smtp\GmailTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SettingRepository $settingRepository, HouseRepository $houseRepository)
    {
        $setting = $settingRepository->findAll();
        $slider = $houseRepository->findBy([], ['title' => 'ASC'], 3);
        $houses = $houseRepository->findBy([], ['title' => 'DESC'], 3);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'setting' => $setting,
            'slider' => $slider,
            'houses' => $houses,

        ]);
    }

    /**
     * @Route("/house/{id}", name="house_show", methods={"GET"})
     */
    public function show(House $house,$id, ImageRepository $imageRepository,CommentRepository $commentRepository): Response
    {
        $images = $imageRepository->findBy(['house'=>$id]);
        $comments = $commentRepository->findBy(['houseid'=>$id, 'status'=>'True']);
        return $this->render('home/houseshow.html.twig', [
            'house' => $house,
            'images' => $images,
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/about", name="home_about")
     */
    public function about(SettingRepository $settingRepository,HouseRepository $houseRepository): Response
    {
        $setting = $settingRepository->findAll();
        $slider = $houseRepository->findBy([], ['title' => 'ASC'], 3);
        $houses = $houseRepository->findBy([], ['title' => 'DESC'], 3);
        return $this->render('home/aboutus.html.twig', [
            'setting' => $setting,
            'slider' => $slider,
            'houses' => $houses,

        ]);
    }
    /**
     * @Route("/footer", name="home_footer")
     */
    public function footer(SettingRepository $settingRepository,HouseRepository $houseRepository): Response
    {
        $setting = $settingRepository->findAll();
        $slider = $houseRepository->findBy([], ['title' => 'ASC'], 3);
        $houses = $houseRepository->findBy( ['title' => 'DESC'], 4);
        return $this->render('home/footer.html.twig', [
            'setting' => $setting,
            'slider' => $slider,
            'houses' => $houses,

        ]);
    }

    /**
     * @Route("/contact", name="home_contact", methods={"GET","POST"})
     */
    public function contact(SettingRepository $settingRepository, Request $request): Response
    {
        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);
        $submittedToken=$request->request->get('token');
        $setting=$settingRepository->findAll();
        if ($form->isSubmitted()) {
            if ($this->isCsrfTokenValid('form-message',$submittedToken)) {
                $entityManager = $this->getDoctrine()->getManager();
                $message->setStatus('New');
                $message->setIp($_SERVER['REMOTE_ADDR']);
                $entityManager->persist($message);
                $entityManager->flush();
                $this->addFlash('success','Your Message has been sent successfuly');

                ////////email//////////
                $email=(new Email())
                    ->from($setting[0]->getSmtpemail())
                    ->to($form['email']->getData())
                    ->subject('AllHouse Your Request')
                    ->html("Dear".$form['name']->getData()."<br>
                                <p>We will evaluate your requests and contact you as soon as possible</p>
                                 Thank You for your message<br>
                                 -------------------------------
                                   <br>".$setting[0]->getCompany()."<br>
                                    Address: ".$setting[0]->getAddress()."<br>
                                    Phone: ".$setting[0]->getPhone()."<br>"
                    );
                $transport=new GmailTransport($setting[0]->getSmtpemail(),$setting[0]->getSmtppassword());
                $mailer=new Mailer($transport);
                $mailer->send($email);
                ///////////mail end///////////
                return $this->redirectToRoute('home_contact');
            }
        }
        $setting = $settingRepository->findAll();
        return $this->render('home/contact.html.twig', [

            'setting' => $setting,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/gallery", name="home_gallery", methods={"GET"})
     */
    public function gallery(SettingRepository $settingRepository, HouseRepository $houseRepository): Response
    {
        $setting = $settingRepository->findAll();
        $slider = $houseRepository->findBy([], ['title' => 'ASC'], 4);
        $houses = $houseRepository->findBy([], ['title' => 'DESC'], 15);

        return $this->render('home/gallery.html.twig', [
            'controller_name' => 'HomeController',
            'setting' => $setting,
            'slider' => $slider,
            'houses' => $houses,

        ]);
    }
}
