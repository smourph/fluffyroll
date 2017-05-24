<?php

namespace FluffyRollBundle\Controller;

use FluffyRollBundle\Entity\Teddy;
use FluffyRollBundle\Form\TeddyType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Teddy controller.
 *
 * @Route("/")
 */
class TeddyController extends Controller
{
    /**
     * Lists all teddy entities.
     *
     * @Route("/", name="_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $teddies = $em->getRepository(Teddy::class)->findAll();

        return $this->render(
            'teddy/index.html.twig',
            [
                'teddies' => $teddies,
            ]
        );
    }

    /**
     * Lists all teddy entities.
     *
     * @Route("/list", name="_list")
     * @Method("GET")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $teddies = $em->getRepository(Teddy::class)->findAll();

        return $this->render(
            'teddy/list.html.twig',
            [
                'teddies' => $teddies,
            ]
        );
    }

    /**
     * Creates a new teddy entity.
     *
     * @Route("/new", name="_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $teddy = new Teddy();
        $form = $this->createForm(TeddyType::class, $teddy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $teddy->getImage();
            $fileName = $teddy->getName().'-'.md5(uniqid()).'.'.$file->guessExtension();

            $file->move(
                $this->getParameter('image_directory'),
                $fileName
            );

            $teddy->setImage($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($teddy);
            $em->flush();

            return $this->redirectToRoute('_show', ['id' => $teddy->getId()]);
        }

        return $this->render(
            'teddy/new.html.twig',
            [
                'teddy' => $teddy,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a teddy entity.
     *
     * @Route("/{id}", name="_show")
     * @Method("GET")
     */
    public function showAction(Teddy $teddy)
    {
        $deleteForm = $this->createDeleteForm($teddy);

        return $this->render(
            'teddy/show.html.twig',
            [
                'teddy' => $teddy,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing teddy entity.
     *
     * @Route("/{id}/edit", name="_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Teddy $teddy)
    {
        $fileNameOld = $teddy->getImage();

        $deleteForm = $this->createDeleteForm($teddy);
        $editForm = $this->createForm(TeddyType::class, $teddy);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            /** @var UploadedFile $file */
            $file = $teddy->getImage();
            $fileName = $teddy->getName().'-'.md5(uniqid()).'.'.$file->guessExtension();

            $file->move(
                $this->getParameter('image_directory'),
                $fileName
            );
            $teddy->setImage($fileName);

            $this->getDoctrine()->getManager()->flush();

            unlink($this->getParameter('image_directory').'/'.$fileNameOld);

            return $this->redirectToRoute('_edit', ['id' => $teddy->getId()]);
        }

        return $this->render(
            'teddy/edit.html.twig',
            [
                'teddy' => $teddy,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Deletes a teddy entity.
     *
     * @Route("/{id}", name="_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Teddy $teddy)
    {
        $form = $this->createDeleteForm($teddy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($teddy);
            $em->flush();
        }

        return $this->redirectToRoute('_index');
    }

    /**
     * Creates a form to delete a teddy entity.
     *
     * @param Teddy $teddy The teddy entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Teddy $teddy)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('_delete', ['id' => $teddy->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
