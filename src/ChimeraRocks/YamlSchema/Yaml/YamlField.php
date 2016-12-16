<?php
namespace ChimeraRocks\YamlSchema\Yaml;

class YamlField
{
    private $name;

    private $type;

    private $length;

    private $options;

    private $constraints;

    /**
     * YamlField constructor.
     * @param $name
     * @param $type
     * @param $length
     * @param $options
     */
    public function __construct($name, $type, $length = null, $options = [], $constraints = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->length = $length;
        $this->options = $options;
        $this->constraints = $constraints;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @param mixed $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return array
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @param array $constraints
     */
    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;
    }

    public function parse()
    {
        $string = '';
        $name = $this->getName();
        $type = $this->getType();
        $length = $this->getLength();
        $string .= $name . ':';
        $string .= $this->createFieldBase($name, $type, $length);

        foreach ($this->getOptions() as $attribute => $value) {
            $string .= $this->addFieldOptions($attribute, $value);
        }

        if (is_array($this->getConstraints())) {
            foreach ($this->getConstraints() as $option) {
                $string .= $this->addConstraintField($option);
            }
        } else {
            $string .= $this->addConstraintField($this->getConstraints());
        }

        return rtrim($string,',');
    }

    public function createFieldBase($name, $type, $length = null)
    {
        if ($length) {
            $type .= "('$name',$length)";
        } else {
            $type .=  "('$name')";
        }
        return $type;
    }

    public function addConstraintField($option)
    {
        return '->' . $option . "()";
    }

    public function addFieldOptions($attribute, $value)
    {
        return '->' . $attribute . "('$value')";
    }
}