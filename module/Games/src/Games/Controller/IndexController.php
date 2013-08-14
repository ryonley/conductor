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
use Games\Entity\Games as Game;
use Games\Entity\Players as Player;

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
            'games_pending' => $games_pending,
            'game_type_id' => $game_type_id
        );



    }

    /**
     * THIS IS THE ACTION THAT IS CALLED WHEN A USER CLICKS TO START A NEW GAME
     *  - This action is called either behind the scenes or it redirects back to the pending action
     */
    public function startAction(){
       // NEED TO PASS THE GAME TYPE IN THE ROUTE
       $game_type_id = $this->params()->fromRoute('id');

        /**
         * May need to explicitly set the times for the game and the player
         */

        // GATHER THE USER ID FROM THE IDENTITY
        if($user = $this->identity()){
           // GET AN AVAILABLE GAME ENTITY WITH THE GAME TYPE ID
           $em = $this->getEntityManager();


           $game_type = $em->find('Available', $game_type_id);

            // CREATE THE GAME
           $game = new Game();
           $game->setGameType($game_type)->setStatus('pending')->setMode(1);
           $em->persist($game);


           // CREATE A NEW PLAYER RECORD WITH THE USER
           $player = new Player();
           $player->setGame($game)->setUser($user)->setTurn(1)->setOutcome(null);
           $em->persist($player);

            $game->getPlayers()->add($player);
            $em->flush();
       }

    }




}
