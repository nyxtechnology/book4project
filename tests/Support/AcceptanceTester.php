<?php

declare(strict_types=1);

namespace Tests\Support;

use Faker\Factory;
use ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Inherited Methods
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor {

  use _generated\AcceptanceTesterActions;

  /**
   * Execute a process command.
   *
   * @param string $command
   *   Command to run.
   *   e.g. "drush en devel -y".
   * @param string $pwd
   *   Working directory.
   *
   * @return string
   *   The process output.
   */

  public function runProcess($command, $pwd = NULL) {
    $command_args = explode(' ', $command);
    $process = new Process($command_args);

    // Set working directory if configured.
    if ($pwd) {
      $process->setWorkingDirectory($pwd);
    }

    try {
      $process->mustRun();

      return $process->getOutput();
    } catch (ProcessFailedException $exception) {
      return $exception->getMessage();
    }
  }

  /**
   * Create user in Drupal.
   * @param $name
   * @param $email
   *
   * @return false|float|int|string
   */
  public function createUser($name, $email) {
    $this->runProcess('drush user:create '.$name.' --mail='.$email.' --password=letmein' , '/var/www/html/web');
    $id = $this->runProcess("drush uinf $name --field=uid");
    if (is_numeric($id))
      return $id;
    else
      return FALSE;
  }

  /**
   * Create user with role and Log in.
   *
   * @param string $role
   *   Role.
   *
   * @return \Drupal\user\Entity\User
   *   User object.
   */
  public function logInWithRole($role) {

    $user = $this->createUserWithRoles([$role], Factory::create()->password(12, 14));

    $this->logInAs($user->getAccountName());

    return $user;
  }

  /**
   * Create test user with specified roles.
   *
   * @param array $roles
   *   List of user roles.
   * @param mixed $password
   *   Password.
   *
   * @return \Drupal\user\Entity\User
   *   User object.
   */
  public function createUserWithRoles(array $roles = [], $password = FALSE) {
    $faker = Factory::create();
    /** @var \Drupal\user\Entity\User $user */
    try {
      $user = \Drupal::entityTypeManager()->getStorage('user')->create([
        'name' => $faker->userName,
        'mail' => $faker->email,
        'roles' => empty($roles) ? $this->_getConfig('default_role') : $roles,
        'pass' => $password ? $password : $faker->password(12, 14),
        'status' => 1,
      ]);

      $user->save();
      $this->users[] = $user->id();
    }
    catch (\Exception $e) {
      $message = sprintf('Could not create user with roles: %s. Error: %s', implode(', ', $roles), $e->getMessage());
      var_dump($message);
    }

    return $user;
  }

  /**
   * Log in user by username.
   *
   * @param string|int $username
   *   User id.
   */
  public function logInAs($username) {
    $output = $this->runProcess('drush uli --name=' . $username, '/var/www/html/web');
    $gen_url = str_replace(PHP_EOL, '', $output);
    $url = substr($gen_url, strpos($gen_url, '/user/reset'));
    $this->amOnPage($url);
  }

  /**
   * Create entity from values.
   *
   * @param array $values
   *   Data for creating entity.
   *   Require:
   *    "bundle" => "basic_page"
   * @param string $type
   *   Entity type.
   * @param bool $validate
   *   Flag to validate entity fields..
   *
   * @return string
   *   ID Entity.
   */
  public function createEntity(array $values = [], $type = 'node', $title = '') {
    $date = new DateTime();
    $bundle = $values['bundle'];
    // login as admin
    $this->logInAs('admin');
    // create panels bi
    $this->amOnPage('/node/add/' . $bundle);
    // Insert the title
    $title = $title != '' ? $title : 'Test of ' .$bundle . ' ' . $date->getTimestamp();
    $this->fillField('#edit-title-0-value', $title);
    $elementPermissions = $this->executeJS('return jQuery("#edit-group-permissions").attr("data-drupal-selector")');
    if($elementPermissions == "edit-group-permissions")
      $this->click("#$elementPermissions");
    //Add values in fields
    foreach ($values as $key => $value) {
      if ($key == 'bundle')
        continue;
      //$typeElement = $this->executeJS('return jQuery("'. $key .'").attr("type")');
      $typeElement = $this->grabAttributeFrom($key, 'type');
      if (!$typeElement){
        $el = $this->findField($key);
        if ($el->getTagName() == 'select')
          $typeElement = 'select';
        if ($el->getTagName() == 'textarea')
          $typeElement = 'textarea';
      }

      switch ($typeElement) {
        case "checkbox":
          $this->checkOption($key);
          break;
        case "select":
          $this->selectOption($key, $value);
          break;
        case "submit":
          $this->click($key);
          break;
        default:
          $this->fillField($key, $value);
      }
    }
    $this->click("#node-$bundle-form input[data-drupal-selector='edit-submit']");
    $id = $this->grabAttributeFrom('.contextual-region.node', 'data-history-node-id');
    return $id;
  }
}