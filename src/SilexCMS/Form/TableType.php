<?php

namespace SilexCMS\Form;

use Pimple;

use SilexCMS\Form\RowType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TableType extends AbstractType
{

    public function __construct(Pimple $container, $table)
    {
        $this->container = $container;
        $this->table = $table;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('rows', 'collection', array('type' => new RowType($this->container, $this->table)));
    }

    public function getName()
    {
        return 'Table' . ucfirst($this->table);
    }

}
