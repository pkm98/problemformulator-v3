<?php
/**
*
* PHP 5
*
* CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
* Copyright 2005-2012, Cake Software Foundation, Inc. 
* (http://cakefoundation.org)
*
* Licensed under The MIT License
* Redistributions of files must retain the above copyright notice.
*
* @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. 
* (http://cakefoundation.org)
* @link          http://cakephp.org CakePHP(tm) Project
* @package       Cake.View.Layouts
* @since         CakePHP(tm) v 0.10.0.1076
* @license       MIT License 
* (http://www.opensource.org/licenses/mit-license.php)
*/

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php 
framework');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php 
            echo $this->Html->charset();
            echo $this->Html->meta('icon');
        ?>
        <title>
            Problem Formulator - an interactive aid for conceptual design
        </title>
        <?php

        //echo $this->Html->css('cake.generic');
        echo $this->Html->css('bootstrap.min');
        echo $this->Html->css('bootstrap-responsive.min');

        echo $this->Html->script('jquery-1.10.1.min');
        echo $this->Html->script('bootstrap.min');

        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');

        echo $this->Js->writeBuffer();
        ?>
    </head>
    <body>
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <?php echo $this->Html->link('Problem Formulator', array('controller' => 
                'pages', 'action' => 'display', 'home'), array('class' => 'brand')); ?>
                <ul class="nav">
                    <?php if($authUser){ ?>
                    <li><?php echo $this->Html->link('View Problem Maps', 
                    array('controller' => 'problem_maps', 'action' => 'index'));?></li>
                    <li><?php echo $this->Html->link('Logout', array('controller' => 
                    'users', 'action' => 'logout')); ?></li>
                    <?php } else{ ?>
                    <li><?php echo $this->Html->link('Create Account', 
                    array('controller' => 'users', 'action' => 'add')); ?></li>
                    <li><?php echo $this->Html->link('Login', array('controller' => 
                    'users', 'action' => 'login')); ?></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="container-fluid">
            <?php echo $this->Session->flash(); ?>
            <?php echo $this->fetch('content'); ?>
        </div>
    </body>
</html>

