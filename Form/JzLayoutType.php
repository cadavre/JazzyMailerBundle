<?php

namespace Jazzy\MailerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Lexik\Bundle\MailerBundle\Form\LayoutType;
use Lexik\Bundle\MailerBundle\Form\LayoutTranslationType;

/**
 * @author Seweryn Zeman <seweryn.zeman@jazzy.pro>
 */
class JzLayoutType extends LayoutType {

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilder $builder, array $options) {
    $builder->add('reference', null, array(
                'read_only' => $options['edit']
            ))
            ->add('description')
            ->add('parent', 'entity', array(
                'required' => false,
                'empty_value' => '',
                'class' => 'Jazzy\MailerBundle\Entity\JzLayout',
            ))
            ->add('translation', new LayoutTranslationType(), array(
                'property_path' => false,
                'data' => $options['data_translation'],
                'with_language' => $options['edit'],
            ));
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOptions(array $options) {
    return array(
        'data_class' => 'Jazzy\MailerBundle\Entity\JzLayout',
        'data_translation' => null,
        'edit' => false,
        'preferred_languages' => array(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'mailer_layout';
  }

}