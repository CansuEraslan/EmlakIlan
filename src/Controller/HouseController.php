<?php

namespace App\Controller;

use App\Entity\House;
use App\Form\House1Type;
use App\Repository\HouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/house")
 */
class HouseController extends AbstractController
{
    /**
     * @Route("/", name="user_house_index", methods={"GET"})
     */
    public function index(HouseRepository $houseRepository): Response
    {
        $user = $this->getUser();
        return $this->render('house/index.html.twig', [
            'houses' => $houseRepository->findBy(['userid'=>$user->getId()]),
        ]);
    }

    /**
     * @Route("/new", name="user_house_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $house = new House();
        $form = $this->createForm(House1Type::class, $house);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            /*******************file upload*****************/
            $file=$form['image']->getData();
            if($file){
                $fileName=$this->generatedUniqueFileName(). '.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('image_directory'),
                        $fileName
                    );
                }catch(FileException $e){

                }
                $house->setImage($fileName);
            }
            /*************file upload**********/
            $user = $this->getUser();
            $house->setUserid($user->getId());
            $house->setStatus("New");
            $entityManager->persist($house);
            $entityManager->flush();

            return $this->redirectToRoute('user_house_index');
        }

        return $this->render('house/new.html.twig', [
            'house' => $house,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_house_show", methods={"GET"})
     */
    public function show(House $house): Response
    {
        return $this->render('house/show.html.twig', [
            'house' => $house,

        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_house_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, House $house): Response
    {
        $form = $this->createForm(House1Type::class, $house);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file=$form['image']->getData();
            if($file){
                $fileName=$this->generatedUniqueFileName(). '.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('image_directory'),
                        $fileName
                    );
                }catch(FileException $e){

                }
                $house->setImage($fileName);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_house_index');
        }

        return $this->render('house/edit.html.twig', [
            'house' => $house,
            'form' => $form->createView(),
        ]);
    }
    private function generatedUniqueFileName(){
        return md5(uniqid());
    }

    /**
     * @Route("/{id}", name="user_house_delete", methods={"DELETE"})
     */
    public function delete(Request $request, House $house): Response
    {
        if ($this->isCsrfTokenValid('delete'.$house->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($house);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_house_index');
    }
}
