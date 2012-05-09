<?php

namespace Jazzy\MailerBundle\Message;

use Lexik\Bundle\MailerBundle\Model\EmailInterface;
use Lexik\Bundle\MailerBundle\Entity\Layout;

/**
 * Render each parts of an email.
 * 
 * Warning! for Twig_Loader_Array class:
 * When using this loader with a cache mechanism, you should know that a new cache
 * key is generated each time a template content "changes" (the cache key being the
 * source code of the template). If you don't want to see your cache grows out of
 * control, you need to take care of clearing the old cache file by yourself.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class MessageRenderer {

  /**
   * @var \Twig_Environment
   */
  private $templating;

  /**
   * Construct
   *
   * @param \Twig_Environment $templating
   * @param array $defaultOptions
   */
  public function __construct(\Twig_Environment $templating) {
    $this->templating = $templating;

    $this->templating->enableStrictVariables();
  }

  /**
   * Load all templates from the email.
   *
   * @param EmailInterface $email
   */
  public function loadTemplates(EmailInterface $email) {
    $layoutsArray = array();
    $content = "";

    $this->templating->getLoader()->setTemplate('subject', $email->getSubject());
    $this->templating->getLoader()->setTemplate('from_name', $email->getFromName());

    $locale = $email->getLocale();

    $startLayout = $email->getLayout();
    $layoutsArray = array_reverse($this->getLayout($startLayout, $locale, $layoutsArray));
    $layoutsCount = count($layoutsArray);

    for ($i = 0; $i < $layoutsCount; $i++) {
      $layoutBody = ($i == 0) ? $layoutsArray[$i]['body'] : "{% extends '" . $layoutsArray[$i - 1]['reference'] . "' %}" . $layoutsArray[$i]['body'] ;
      $this->templating->getLoader()->setTemplate($layoutsArray[$i]['reference'], $layoutBody);
    }
    $content = ($layoutsCount != 0) ? "{% extends '" . $layoutsArray[$layoutsCount - 1]['reference'] . "' %}" . $email->getBody() : $email->getBody() ;

    $this->templating->getLoader()->setTemplate('content', $content);
  }

  private function getLayout($layout, $locale, $layoutsArray) {
    $layout->setLocale($locale);

    $reference = $layout->getReference();
    $body = $layout->getBody();
    $layoutsArray[] = array('reference' => $reference, 'body' => $body);

    $parent = $layout->getParent();
    if ($parent instanceof Layout) {
      $layoutsArray = $this->getLayout($parent, $locale, $layoutsArray);
    }

    return $layoutsArray;
  }

  /**
   * Render a template previously loaded.
   *
   * @param string $view
   * @param array $parameters
   * @return string
   */
  public function renderTemplate($view, array $parameters = array()) {
    return $this->templating->render($view, $parameters);
  }

  /**
   * Enable or not strict variables.
   *
   * @param boolean $strict
   */
  public function setStrictVariables($strict) {
    if ((bool) $strict) {
      $this->templating->enableStrictVariables();
    } else {
      $this->templating->disableStrictVariables();
    }
  }

}