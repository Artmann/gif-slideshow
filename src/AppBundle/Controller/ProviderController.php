<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Providers\GiphyProvider;
use AppBundle\Entity\Providers\Provider;
use AppBundle\Entity\Providers\RedditProvider;
use AppBundle\Entity\Slideshow;
use AppBundle\Form\Providers\GiphyProviderType;
use AppBundle\Form\Providers\RedditProviderType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProviderController
 * @package AppBundle\Controller
 */
class ProviderController extends Controller
{

    /**
     * Creates a new Provider entity.
     *
     * @Route("/slideshow/{id}/addprovider/{type}", name="provider_add")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, Slideshow $slideshow, $type)
    {

        if($type == 'reddit'){
            $provider = new RedditProvider();
            $provider->setSlideshow($slideshow);
            $form = $this->createForm(RedditProviderType::class, $provider);
        } elseif($type == 'giphy'){
            $provider = new GiphyProvider();
            $provider->setSlideshow($slideshow);
            $form = $this->createForm(GiphyProviderType::class, $provider);
        } else {
            throw $this->createNotFoundException("Invalid provider type");
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($provider);
            $em->flush();

            return $this->redirectToRoute('slideshow_edit', array('id' => $slideshow->getId()));
        }

        return $this->render('provider/new.html.twig', array(
            'type' => $type,
            'slideshow' => $slideshow,
            'provider' => $provider,
            'form' => $form->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing Provider entity.
     *
     * @Route("/provider/{id}/edit", name="provider_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Provider $provider)
    {
        $deleteForm = $this->createDeleteForm($provider);
        if($provider instanceof RedditProvider){
            $editForm = $this->createForm(RedditProviderType::class, $provider);
        } elseif($provider instanceof GiphyProvider){
            $editForm = $this->createForm(GiphyProviderType::class, $provider);
        } else {
            throw $this->createNotFoundException('Invalid provider type');
        }

        $slideshow = $provider->getSlideshow();
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($provider);
            $em->flush();

            return $this->redirectToRoute('slideshow_edit', array('id' => $slideshow->getId()));
        }

        return $this->render('provider/edit.html.twig', array(
            'slideshow' => $slideshow,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Slideshow entity.
     *
     * @Route("/{id}", name="provider_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Provider $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Provider $provider)
    {
        $form = $this->createDeleteForm($provider);
        $slideshow = $provider->getSlideshow();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($provider);
            $em->flush();
        }

        return $this->redirectToRoute('slideshow_edit', ['id' => $slideshow->getId()]);
    }

    /**
     * Creates a form to delete a Slideshow entity.
     *
     * @param Provider $provider
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Provider $provider)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('provider_delete', array('id' => $provider->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }


}
