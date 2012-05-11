<?php

namespace Jazzy\MailerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

use Lexik\Bundle\MailerBundle\Exception\NoTranslationException;
use Lexik\Bundle\MailerBundle\Model\LayoutInterface;
use Lexik\Bundle\MailerBundle\Entity\Layout;

/**
 * @ORM\Entity
 *
 * @author Seweryn Zeman <seweryn.zeman@jazzy.pro>
 */
class JzLayout extends Layout
{

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Jazzy\MailerBundle\Entity\JzLayout")
     */
    protected $parent;
    
    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $arguments;

    /**
     * Get parent
     *
     * @return JzLayout
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent
     *
     * @param JzLayout $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get arguments
     *
     * @return string
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Set arguments
     *
     * @param string $arguments
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

}