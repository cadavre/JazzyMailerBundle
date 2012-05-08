<?php

namespace Jazzy\MailerBundle\Controller;

use Lexik\Bundle\MailerBundle\Controller\LayoutController;
use Lexik\Bundle\MailerBundle\Entity\LayoutTranslation;

use Jazzy\MailerBundle\Entity\JzLayout;
use Jazzy\MailerBundle\Form\JzLayoutType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Locale\Locale;

/**
 * Layout controller.
 *
 * @author Seweryn Zeman <seweryn.zeman@jazzy.pro>
 */
class JzLayoutController extends LayoutController
{
    /**
     * List all layouts
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $layouts = $this->get('doctrine.orm.entity_manager')->getRepository('JazzyMailerBundle:JzLayout')->findAll();

        return $this->container->get('templating')->renderResponse('JazzyMailerBundle:Layout:list.html.twig', array(
            'layouts'   => $layouts,
            'layout'    => $this->container->getParameter('lexik_mailer.base_layout'),
        ));
    }

    /**
     * Layout edition
     *
     * @param string $layoutId
     * @param string $lang
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($layoutId, $lang = null)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $request = $this->get('request');
        $lang = $lang ? : $this->container->getParameter('locale');

        $layout= $em->find('JazzyMailerBundle:JzLayout', $layoutId);
        $translation = $layout->getTranslation($lang);

        if (!$layout) {
            throw new NotFoundHttpException('Layout not found');
        }

        $form = $this->createForm(new JzLayoutType(), $layout, array(
                    'data_translation'      => $translation,
                    'edit'                  => true,
                ));

        // Submit form
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em->persist($translation);
                $em->flush();

                return $this->redirect($this->generateUrl('lexik_mailer.layout_edit', array(
                            'layoutId'   => $layout->getId(),
                            'lang'      => $lang,
                        )));
            }
        }

        return $this->render('LexikMailerBundle:Layout:edit.html.twig', array(
            'form'          => $form->createView(),
            'base_layout'   => $this->container->getParameter('lexik_mailer.base_layout'),
            'layout'        => $layout,
            'lang'          => $lang,
            'displayLang'   => Locale::getDisplayLanguage($lang),
            'routePattern'  => urldecode($this->generateUrl('lexik_mailer.layout_edit', array('layoutId' => $layout->getId(), 'lang' => '%lang%'), true)),
        ));
    }

    /**
     * Delete layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($layoutId)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $layout = $em->find('JazzyMailerBundle:JzLayout', $layoutId);

        if (!$layout) {
            throw new NotFoundHttpException('Layout not found');
        }

        $layout->getTranslations()->forAll(function($key, $translation) use ($em) {
            $em->remove($translation);
        });

        $em->remove($layout);
        $em->flush();

        return $this->redirect($this->generateUrl('lexik_mailer.layout_list'));
    }

    /**
     * New layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $request = $this->get('request');
        $layout = new JzLayout();
        $translation = new LayoutTranslation($this->container->getParameter('locale'));

        $translation->setLayout($layout);
        $form = $this->createForm(new JzLayoutType(), $layout, array(
                    'data_translation' => $translation,
                ));
        // Submit form
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->persist($translation);
                $em->persist($layout);
                $em->flush();

                return $this->redirect($this->generateUrl('lexik_mailer.layout_new'));
            }
        }

        return $this->render('JazzyMailerBundle:Layout:new.html.twig', array(
            'form'      => $form->createView(),
            'layout'    => $this->container->getParameter('lexik_mailer.base_layout'),
            'lang'      => Locale::getDisplayLanguage($translation->getLang()),
        ));
    }
}