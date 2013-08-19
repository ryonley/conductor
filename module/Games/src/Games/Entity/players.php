<?php
namespace Games\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 */
class Players
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Games", inversedBy="players")
     */
    protected $game;

    protected $game_id;



    /**
     * @ORM\ManyToOne(targetEntity="RelyAuth\Entity\User", inversedBy="players")
     */
    protected $user;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $turn;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $time_joined;

    /**
     * @ORM\Column(type="string")
     */
    protected $outcome;

    /**
     * @ORM\Column(type="string")
     */
    protected $mark;

    /**
     * @param mixed $mark
     */
    public function setMark($mark)
    {
        $this->mark = $mark;
    }

    /**
     * @return mixed
     */
    public function getMark()
    {
        return $this->mark;
    }




    public function hasPendingGame(){
        $gameStatus = $this->game->getStatus();
        if('pending' == $gameStatus) return true;
            else return false;
    }



    /**
     * @return mixed
     */
    public function getGame()
    {
        return $this->game;

    }

    /**
     * @param mixed $game
     */
    public function setGame($game)
    {
        $this->game = $game;
    }



    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $outcome
     */
    public function setOutcome($outcome)
    {
        $this->outcome = $outcome;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOutcome()
    {
        return $this->outcome;
    }

    /**
     * @param mixed $time_joined
     */
    public function setTimeJoined($time_joined)
    {
        $this->time_joined = $time_joined;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeJoined()
    {
        return $this->time_joined;
    }

    /**
     * @param mixed $turn
     */
    public function setTurn($turn)
    {
        $this->turn = $turn;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTurn()
    {
        return $this->turn;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }



}