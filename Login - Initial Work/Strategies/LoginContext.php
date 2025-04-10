<?php
require_once 'NormalLogin.php';
require_once 'GoogleLogin.php';

class LoginContext {
    private $loginStrategy;

    public function setStrategy(LoginStrategy $strategy) {
        $this->loginStrategy = $strategy;
    }

    public function executeLogin($data) {
        $this->loginStrategy->login($data);
    }
}
?>
