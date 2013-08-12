<?php
namespace Games\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Moves
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Games")
     */
    protected $game;

    /**
     * @ORM\ManyToOne(targetEntity="Players")
     */
    protected $player;

    /**
     * @ORM\ManyToOne(targetEntity="Positions")
     */
    protected $position;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $timestamp;



}