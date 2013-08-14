<?php

namespace SilexCMS\Form;

use SilexCMS\Application;

use SilexCMS\Form\RowType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TableType extends AbstractType
{
    public function __construct(Application $app, $table)
    {
        $this->app = $app;
        $this->table = $table;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('row', 'collection', array('type' => new RowType($this->app, $this->table)));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'Table' . ucfirst($this->table);
    }
}
