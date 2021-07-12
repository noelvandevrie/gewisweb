<?php

namespace Photo\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\I18n\Translator;
use Zend\Validator\StringLength;

class CreateAlbum extends Form
{

    public function __construct(Translator $translate)
    {
        parent::__construct();

        $this->add(
            [
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'options' => [
                'label' => $translate->translate('Album title')
            ]
            ]
        );

        $this->add(
            [
            'name' => 'submit',
            'type' => 'submit',
            'options' => [
                'label' => $translate->translate('Create')
            ]
            ]
        );

        $this->initFilters();
    }

    protected function initFilters()
    {
        $filter = new InputFilter();

        $filter->add(
            [
            'name' => 'name',
            'required' => true,
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 3,
                        'max' => 75
                    ]
                ]
            ]
            ]
        );

        $this->setInputFilter($filter);
    }
}
