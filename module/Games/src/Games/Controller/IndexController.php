<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Games\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Config\Config as Config;
use RelyAuth\Entity\User as User;
//use Games\Entity\Available_Games;

class IndexController extends AbstractActionController
{
    protected $em;


    public function getEntityManager(){
        if(null === $this->em){
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }


    /**
     * SHOW AVAILABLE GAMES (TIC TAC TOE, MONOPOLY, ETC
     */
    public function indexAction()
    {
        $em = $this->getEntityManager();
        $games_available = $em->getRepository('Games\Entity\Available')->findAll();

        // Retrieve all of the available games from the db

       $layout = $this->layout();
       $layout->setTemplate('layout/dashboard');

        return array(
          'games_available' => $games_available
        );

    }

    /**
     * THIS IS THE PAGE THAT SHOWS THE GAMES THAT ARE AWAITING PLAYERS AND HAS A LINK TO CREATE A NEW GAME
     */
    public function pendingAction(){
        $game_type_id = $this->params()->fromRoute('id');

        // QUERY THE GAMES TABLE WITH THE GAME TYPE ID
        $em = $this->getEntityManager();
        $games_pending = $em->getRepository('Games\Entity\Games')->findBy(array('game_type' => $game_type_id, 'status' => 'pending'));

        /**
         * DATA NEEDED BY THE VIEW INCLUDES
         *  - THE USERNAME OF THE PLAYER ATTACHED TO THE GAME
         *  - THE TIME THE GAME WAS STARTED
         *
         */

        return array(
            'games_pending' => $games_pending
        );



    }

    /**
     * THIS IS THE ACTION THAT IS CALLED WHEN A USER CLICKS TO START A NEW GAME
     */
    public function startAction(){

    }




}
