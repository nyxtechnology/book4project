<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
  public function findField($key)
  {
    $elements = $this->getModule("WebDriver")->_findElements($key);
    return reset($elements);
  }
}