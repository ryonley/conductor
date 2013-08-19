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
use Games\Entity\Available as Available;
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



        // GATHER THE USER ID FROM THE IDENTITY
        if($user = $this->identity()){
           // GET AN AVAILABLE GAME ENTITY WITH THE GAME TYPE ID
           $em = $this->getEntityManager();


           $game_type = $em->find('Games\Entity\Available', $game_type_id);

           $datetime = new \DateTime("now");

            // CREATE THE GAME
           $game = new Game();
           $game->setGameType($game_type)->setTimeStarted($datetime)->setStatus('pending')->setMode(1);
           $em->persist($game);


           // CREATE A NEW PLAYER RECORD WITH THE USER
           $player = new Player();
            /**
             * FOR NOW THE PLAYER THAT STARTS THE GAME WILL ALWAYS BE X
             */
            $player->setTimeJoined($datetime)->setTurn(1)->setOutcome('1')->setMark('x');
           $em->persist($player);

            $user->getPlayers()->add($player);
            $player->setUser($user);

            $game->getPlayers()->add($player);
            $player->setGame($game);
            $em->flush();
       }


        return $this->redirect()->toRoute('games', array('action' => 'pending', 'id' => $game_type_id));

    }



    public function joinAction(){
        // WHAT INFORMATION DO WE NEED
        // GAME ID
        $em = $this->getEntityManager();

        $game_id = $this->params()->fromRoute('id');
        // RETRIEVE THE GAME
        $game = $em->find('Games\Entity\Games', $game_id);
        $game_type = $game->getGameType();
        $game_name = $game_type->getName();
        $game_name_nospace = str_replace(" ", "", $game_name);
        $game_name_dashes = str_replace(" ", "-", $game_name);
        // make game name dashes lower case
        $game_name_dashes = strtolower($game_name_dashes);

        $datetime = new \DateTime("now");

        if($user = $this->identity()){

            /**
             * THE TURN WAS SET TO TRUE FOR THE PLAYER WHO CREATED THE GAME
             * CAN SAFELY SET THIS PLAYERS TURN TO 0 (FALSE) FOR NOW)
             */
            $player = new Player();
            /**
             * FOR NOW THE PLAYER THAT 'JOINS' THE GAME WILL ALWAYS BE O
             */
            $player->setTimeJoined($datetime)->setOutcome('1')->setTurn(0)->setMark('o');
            $em->persist($player);

            // ASSIGN THE PLAYER TO THE USER
            $user->getPlayers()->add($player);
            $player->setUser($user);

            // ASSIGN THE PLAYER TO THE GAME
            $game->getPlayers()->add($player);
            $player->setGame($game);

            $em->flush();



            // FIND OUT HOW MANY PLAYERS THIS GAME TYPE REQUIRES
            // FIND THE GAME TYPE FROM THE GAME OBJECT
            $game_type = $game->getGameType();
            $minimum_players_needed = $game_type->getMinimumPlayers();

            // HOW MANY PLAYERS DOES THIS GAME NOW HAVE
            /**
             * THIS MAY NOT BE THE CORRECT WAY TO GET THE COUNT
             */
            $player_count = $game->getPlayers()->count();

            // NOW UPDATE THE GAME STATUS TO ACTIVE
            if($player_count >= $minimum_players_needed){
                $game->setStatus('active');
                // IN THIS CASE REDIRECT TO THE PLAY ACTION
                $player_id = $player->getId();
                return $this->redirect()->toRoute($game_name_nospace, array('action' => 'index', 'game_id' => $game_id, 'player_id' => $player_id));
               // $url = "/".$game_name_dashes."/".$game_id;
                //return $this->redirect()->toUrl($url);
            } else {
                // IN THIS CASE THE PLAYER COUNT THAT IS DISPLAYED IN THE GAME BOX NEEDS TO BE UPDATED
                // THIS WILL BE DONE WITH AJAX
            }

        }


    }



    public function playAction(){
        // FIND OUT IF IT IS THIS USERS TURN... IF SO, NOTIFY THEM

        // THE VIEW FOR THIS ACTION WILL DEPEND HEAVILY ON WHICH TYPE OF GAME IS BEING PLAYED (EACH GAME TYPE NEEDS ITS OWN VIEW)

        // DOES IT MAKE SENSE TO HAVE THE VIEW SELECTED DYNAMICALLY BASED ON THE GAME TYPE... OR TO HAVE A SEPARATE ACTION FOR EACH GAME TYPE

        // IT MIGHT ACTUALLY BE BETTER TO HAVE A SEPARATE CONTROLLER FOR EACH GAME....
        // THE REDIRECT ABOVE... INSTEAD OF SENDING TO THE PLAY ACTION, IT WOULD DETERMINE WHICH CONTROLLER TO SEND TO BASED ON THE NAME OF THE GAME
        // EX.  IF THE GAME IS "TIC TAC TOE" WE WILL BE SENT TO THE Tic_Tac_Toe CONTROLLERS PLAY ACTION WITH THE GAME ID AS A PARAMETER

        // -------------------------------

        // THE PLAY ACTION WILL  FIND OUT WHO'S TURN AND NOTIFY THEM

        // NEED A WAY TO MAP A POSITION FROM THE POSITIONS TABLE TO A PLACE ON THE SCREEN (IN THE VIEW)

        // IN THE PLAY ACTION, AFTER THE USER IS NOTIFIED OF THEIR TURN, THAT USER HAS THE ABILITY TO MAKE A "MOVE" ..
        // IN THIS CASE, THERE IS NO LISTENER RUNNING.... THE LISTENER IS IMPLEMENTED BASED ON WHO'S TURN IT IS..
        // IF IT IS NOT YOUR TURN, THEN YOU CANNOT MAKE A MOVE AND A LISTENER IS ENGAGED

        // IF IT IS YOUR TURN YOU CAN MAKE A MOVE... YOU SUBMIT YOUR MOVE VIA AJAX, THE DATABASE IS UPDATED, YOUR TURN IS SET TO 0, AND THE LISTENER IS ENGAGED FOR YOU
        // ONCE IT DETERMINES THAT A MOVE HAS BEEN MADE, IT UPDATES THE VIEW WITH THE MOVES
        // AND LETS YOU KNOW IF IT IS YOUR TURN OR NOT
        // AND DECIDES WHEATHER TO KEEP THE LISTENER ENGAGED

    }




}
