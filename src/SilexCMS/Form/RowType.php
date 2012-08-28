<?php

namespace SilexCMS\Form;

use Pimple;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RowType extends AbstractType
{

    public function __construct(Pimple $container, $table)
    {
        $this->container = $container;
        $this->table = $table;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $schemaManager = $this->container['db']->getSchemaManager();

        foreach ($schemaManager->listTableColumns($this->table) as $column) {
            switch ($column->getType()) {
                case 'Integer':
                    $builder->add($column->getName(), 'integer', array('read_only' => $column->getName() === 'id' ? true : false));
                break;

                case 'String':
                    $builder->add($column->getName(), 'text');
                break;

                case 'Text':
                    $builder->add($column->getName(), 'textarea');
                break;
            }
        }
    }

    public function getName()
    {
        return 'Row' . ucfirst($this->table);
    }

}
