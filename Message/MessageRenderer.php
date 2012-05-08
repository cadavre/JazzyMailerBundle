<?php

namespace Jazzy\MailerBundle\Message;

use Lexik\Bundle\MailerBundle\Model\EmailInterface;

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
    $this->templating->getLoader()->setTemplate('subject', $email->getSubject());
    $this->templating->getLoader()->setTemplate('from_name', $email->getFromName());

    $layout = $email->getLayoutBody();
    $this->templating->getLoader()->setTemplate('layout', $layout);

    $content = empty($layout) ? $email->getBody() : '{% extends \'layout\' %}' . $email->getBody() . '';
    $this->templating->getLoader()->setTemplate('content', $content);
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