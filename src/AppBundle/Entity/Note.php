<?php
/**
 * Created by PhpStorm.
 * User: nikola
 * Date: 20.7.17.
 * Time: 21.14
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class Note
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NoteRepository")
 * @ORM\Table(name="note")
 */

class Note implements \JsonSerializable
{
    /**
     * @ORM\Column(type="integer")`
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $content;
    /**
     * @var string;
     * @ORM\Column(type="string")
     */
    private $color='white';

    /**
     * @var boolean
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $trashed=false;

    /**
     * @return mixed
     */
    public function getTrashed()
    {
        return $this->trashed;
    }

    /**
     * @param mixed $trashed
     */
    public function setTrashed($trashed)
    {
        $this->trashed = $trashed;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }


    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'content' => $this->content,
            'color' => $this->color,
            'trashed' => $this->trashed
        ];
    }
}