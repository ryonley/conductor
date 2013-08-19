<?php
namespace Games\Entity;

use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Games
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Available", inversedBy="games")
     */
    protected $game_type;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $time_started;

    /**
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     * @ORM\Column(type="integer")
     */
    protected $mode;

    /**
     *  @ORM\OneToMany(targetEntity="Players", mappedBy="game")
     */
    protected $players;

    public function __construct(){
        $this->players = new ArrayCollection();
    }

    public function getPlayers(){
        return $this->players;
    }



    /**
     * @param mixed $game_type
     */
    public function setGameType($game_type)
    {
        $this->game_type = $game_type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGameType()
    {
        return $this->game_type;
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
     * @param mixed $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $time_started
     */
    public function setTimeStarted($time_started)
    {
        $this->time_started = $time_started;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeStarted()
    {
        return $this->time_started;
    }



}