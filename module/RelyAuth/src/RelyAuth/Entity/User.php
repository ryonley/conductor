<?php

namespace RelyAuth\Entity;

use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Crypt\Password\Bcrypt;
use Games\Entity\Players;

/**
 * @ORM\Entity
 */
class User implements InputFilterAwareInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $username;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @ORM\Column(type="string")
     */
    protected $real_name;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     *
     */
    protected $role;

    /**
     * @param mixed $role
     */


    /**
     *  @ORM\OneToMany(targetEntity="Players", mappedBy="user")
     */
    protected $players;

    public function __construct(){
        $this->players = new ArrayCollection();
    }


    public function setRole($role)
    {
        $this->role = $role;
    }



    const SALT = '1234567890123456';

    protected $inputFilter;

    public function __contruct(){

    }


    public function __get($property){
        return $this->$property;
    }

    public function __set($property, $value){
        $this->property = $value;
    }

    public function getRealName(){
        return $this->real_name;
    }

    public function getRole(){
        return $this->role;
    }



    public function getUserName(){
        return $this->username;
    }

    public function getArrayCopy(){
        return get_object_vars($this);
    }

    public function getPassword(){
        return $this->password;
    }

    public function setPassword($password){
        $bcrypt = new Bcrypt(array(
            'salt' => self::SALT,
            'cost' => 14
        ));
        $this->password = $bcrypt->create($password);
        return $this;
    }


    public static function hashPassword($user, $password){
        $c = get_called_class();
        $salt = $c::SALT;
        $bcrypt = new Bcrypt(array(
            'salt' => $salt,
            'cost' => 14
        ));
        if($bcrypt->verify($password, $user->getPassword())){
            return true;
        } else return false;
    }

    public function setRealName($name){
        $this->real_name = $name;
        return $this;
    }

    public function setUserName($username){
        $this->username = $username;
        return $this;
    }

    public function populate($data = array()){
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
        $this->real_name = (isset($data['real_name'])) ? $data['real_name'] : null;
    }

    
    public function setInputFilter(InputFilterInterface $inputFilter) 
    {
         throw new \Exception("Not used");
    }
    
    public function getInputFilter()
    {
         if(!$this->inputFilter){
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'email',
                'required' => true,
                   'filters' => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim')
                 ),
                 'validators' => array(
                     array(
                         'name' => 'EmailAddress'
                     )
                 )
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' =>'password',
                'required' => true,
                 'filters' => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim')
                 ),
            )));
            
            $this->inputFilter = $inputFilter;
         }
         return $this->inputFilter;
    }
}
