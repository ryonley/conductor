<?php

namespace Games\Controller;

use Games\Entity\Moves;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Config\Config as Config;
use RelyAuth\Entity\User as User;
use Doctrine\Common\Collections\Criteria;

class TicTacToeController extends AbstractActionController
{

    public function getEntityManager(){
        if(null === $this->em){
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    // THE PLAY ACTION WILL  FIND OUT WHO'S TURN AND NOTIFY THEM

    // NEED A WAY TO MAP A POSITION FROM THE POSITIONS TABLE TO A PLACE ON THE SCREEN (IN THE VIEW)

    // IN THE PLAY ACTION, AFTER THE USER IS NOTIFIED OF THEIR TURN, THAT USER HAS THE ABILITY TO MAKE A "MOVE" ..
    // IN THIS CASE, THERE IS NO LISTENER RUNNING.... THE LISTENER IS IMPLEMENTED BASED ON WHO'S TURN IT IS..
    // IF IT IS NOT YOUR TURN, THEN YOU CANNOT MAKE A MOVE AND A LISTENER IS ENGAGED

    // IF IT IS YOUR TURN YOU CAN MAKE A MOVE... YOU SUBMIT YOUR MOVE VIA AJAX, THE DATABASE IS UPDATED, YOUR TURN IS SET TO 0, AND THE LISTENER IS ENGAGED FOR YOU
    // ONCE IT DETERMINES THAT A MOVE HAS BEEN MADE, IT UPDATES THE VIEW WITH THE MOVES
    // AND LETS YOU KNOW IF IT IS YOUR TURN OR NOT
    // AND DECIDES WHEATHER TO KEEP THE LISTENER ENGAGED
    public function indexAction(){
        $em = $this->getEntityManager();
        $game_id = $this->params()->fromRoute('game_id');
        $player_id = $this->params()->fromRoute('player_id');
        // PLAYER ID NEEDS TO BE SET IN THE SESSION OR MAYBE NOT SINCE THE URL SHOULDN'T CHANGE WHILE WE ARE PLAYING


        $user = $this->identity();

        // FIND OUT IF IT IS THIS PLAYERS TURN
        $player = $em->find('players', $player_id);
        $turn = $player->getTurn();

        // IF TURN IS TRUE, THE USER WILL BE ABLE TO CLICK THE TICK TAC TOE ELEMENTS IN THE VIEW
        // OTHERWISE THEY WILL BE DISABLED


    }

    /**
     * THIS ACTION FIRST MAKES SURE THE POSITION HAS NOT ALREADY BEEN TAKEN
     * IT CREATES A RECORD IN THE MOVES TABLE FOR THIS POSITION
     * SETS THE PLAYERS TURN TO 0
     * LETS THE CLIENT KNOW IF OPERATIONS WERE SUCCESSFULL OR NOT..
     * ALSO IT TELLS THE CLIENT WHETHER TO MARK WITH AN X OR AN O
     */
    public function moveAction(){
        // INFO NEEDED
        // POSITION ID
        // PLAYER ID
        // GAME ID
        $game_id = $this->params()->fromRoute('game_id');
        $player_id = $this->params()->fromRoute('player_id');
        $request = $this->getRequest();
        $response = $this->getResponse();

        if($request->isPost()){
            $post_data = $request->getPost();
            $position_id = $post_data['position_id'];

            $em = $this->getEntityManager();
            // GET THE  GAME OBJECT, PLAYER OBJECT, AND POSITION OBJECT
            $game = $em->find('games', $game_id);
            $player = $em->find('players', $player_id);
            $position = $em->find('positions', $position_id);

            // MAKE SURE THERE IS NOT ALREADY A RECORD IN THE MOVES TABLE FOR THIS POSITION AND GAME

            $moves = $em->getRepository('Games\Entity\Moves')->findBy(array('game_id' => $game_id, 'player_id' => $player_id, 'position_id' => $position_id))->count();
            if($moves > 0){
                // THIS POSITION WAS ALREADY TAKEN
                $success = false;
            } else {
                // THIS POSITION IS AVAILABLE... CREATE THE RECORD
                $datetime = new \DateTime("now");
                $move = new Moves();
                $em->persist($move);
                $move->setGame($game)->setPlayer($player)->setPosition($position)->setTimestamp($datetime);

                $player->setTurn(0);
                $em->flush();

                $move_time_object = $move->getTimestamp();
                $move_time = $move_time_object->format('Y-m-d H:i:s');
                $success = true;

            }

             $response->setContent(\Zend\Json\Json::encode(array('success' => $success, 'mark' => $player->getMark(), 'timestamp' => $move_time, 'game_id' => $game_id)));
        }

    }


    /**
     * THE UPDATE FUNCTION NEEDS A WAY TO CHECK IF A CHANGE HAS BEEN MADE
     *
     * WE WILL SEND THE FUNCTION A TIMESTAMP AND IT WILL CHECK THE MOVES TABLE TO SEE IF THERE IS A MORE RECENT TIMESTAMP
     * FOR THIS GAME
     *
     * IF THERE IS, THE UPDATE ACTION WILL RETURN THE POSITION ID AND THE MARK OF THE PLAYER HOW MADE THE MOVE
     */
    public function updateAction(){
      // RECEIVE THE TIMESTAMP AND GAME_ID

      // CHECK THE MOVES TABLE FOR A RECORD WITH A TIMESTAMP GREATER THAN THAT PROVIDED AND A MATCHING GAME ID
      // IF IT EXISTS RETURN TRUE

      // NEEDS TO RETURN WHETER OR NOT IT IS THE PLAYERS TURN (MAYBE RETURN THE  ID OF THE PLAYER WHO'S TURN IT IS)

      // ALSO RETURN THE POSITION ID AND THE MARK OF THE PLAYER WHO MADE THE MOVE

    }

}
