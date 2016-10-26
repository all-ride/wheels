<?php

namespace ride\wheel\form;

use ride\library\form\component\AbstractComponent;
use ride\library\form\FormBuilder;
use ride\library\i18n\translator\Translator;

class ExampleForm extends AbstractComponent {

    /**
     * Prepares the form
     *
     * @param FormBuilder $builder
     * @param array       $options
     *
     * @return void
     */
    public function prepareForm(FormBuilder $builder, array $options) {
        /** @var Translator $translator */
        $translator = $options['translator'];

        $builder->addRow('name', 'string', [
            'label'      => $translator->translate('label.name'),
            'validators' => [
                'required'  => [],
                'trim'      => [],
                'stripTags' => [],
            ],
        ]);

        $builder->addRow('message', 'text', [
            'label'       => $translator->translate('label.example.message'),
            'description' => $translator->translate('label.example.message.description'),
            'validators'  => [
                'required'  => [],
                'trim'      => [],
                'stripTags' => [],
            ],
        ]);

        $builder->addRow('image', 'file', [
            'label'       => $translator->translate('label.example.image'),
            'description' => $translator->translate('label.example.image.description'),
            'validators'  => [
                'required'  => [],
            ],
        ]);
    }
}
