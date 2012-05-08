<?php

namespace Jazzy\MailerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class JazzyMailerBundle extends Bundle {

  public function getParent() {
    return 'LexikMailerBundle';
  }

}
