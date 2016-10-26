<?php

namespace ride\wheel\form;

use ride\library\cms\node\NodeModel;
use ride\library\form\component\AbstractComponent;
use ride\library\form\FormBuilder;
use ride\library\i18n\I18n;
use ride\library\i18n\translator\Translator;

class ExamplePropertiesForm extends AbstractComponent {

    /** @var array */
    protected $templates;

    /** @var array */
    protected $nodes;

    /**
     * Inject the NodeModel and I18n so all nodes can be loaded.
     *
     * @param NodeModel $nodeModel
     * @param I18n      $i18n
     */
    public function __construct(NodeModel $nodeModel, I18n $i18n) {
        $this->nodes = $nodeModel->getListFromNodes(
            $nodeModel->getNodes('ride', 'master'),
            $i18n->getLocale()->getCode()
        );
    }

    /**
     * @param array $templates
     */
    public function setTemplates(array $templates) {
        $this->templates = $templates;
    }

    /**
     * @param array $nodes
     */
    public function setNodes(array $nodes) {
        $this->nodes = $nodes;
    }

    /**
     * Prepares the form
     *
     * @param FormBuilder $builder
     * @param array       $options
     *
     * @return void
     * @throws \Exception
     */
    public function prepareForm(FormBuilder $builder, array $options) {
        // Throw an exception when no templates are set.
        if (empty($this->templates)) {
            throw new \Exception('No templates were set');
        }

        /** @var Translator $translator */
        $translator = $options['translator'];

        $builder->addRow('title', 'string', [
            'label'      => $translator->translate('label.title'),
            'validators' => [
                'required'  => [],
                'trim'      => [],
                'stripTags' => [],
            ],
        ]);

        $builder->addRow('image', 'assets', array(
            'label' => $translator->translate('label.image'),
        ));

        $builder->addRow('template', 'select', array(
            'label'   => $translator->translate('label.template'),
            'options' => $this->templates,
        ));
    }
}
