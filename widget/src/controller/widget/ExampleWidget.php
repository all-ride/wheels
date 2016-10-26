<?php

namespace ride\wheel\controller\widget;

use ride\application\orm\asset\model\AssetModel;
use ride\library\cms\node\NodeModel;
use ride\library\validation\exception\ValidationException;
use ride\wheel\form\ExampleForm;
use ride\wheel\form\ExamplePropertiesForm;
use ride\wheel\orm\model\ExampleModel;
use ride\web\cms\controller\widget\AbstractWidget;

/**
 * This example widget can be configured in the backend with a title, image, redirect node and template.
 * The indexAction of this widget will render a form for user input, which will be saved upon submission.
 */
class ExampleWidget extends AbstractWidget {

    /**
     * The widget's name
     */
    const NAME = 'wheel.example';

    /**
     * The template namespace of this widget
     */
    const TEMPLATE_NAMESPACE = 'cms/widget/example';

    /**
     * The default template name
     */
    const TEMPLATE_DEFAULT = 'default';

    /**
     * @var ExampleModel
     */
    private $exampleModel;

    /**
     * @var AssetModel
     */
    private $assetModel;

    /**
     * @var NodeModel
     */
    private $nodeModel;

    /**
     * Inject models instead of the ORM manager, see dependencies.json
     *
     * @param ExampleModel $exampleModel
     * @param AssetModel   $assetModel
     * @param NodeModel    $nodeModel
     */
    public function __construct(ExampleModel $exampleModel, AssetModel $assetModel, NodeModel $nodeModel) {
        $this->exampleModel = $exampleModel;
        $this->assetModel = $assetModel;
        $this->nodeModel = $nodeModel;
    }

    /**
     * @param ExampleForm $exampleForm
     *
     * @return void
     */
    public function indexAction(ExampleForm $exampleForm) {
        // Load all required properties.
        $properties = [
            'title'    => $this->properties->getLocalizedWidgetProperty($this->locale, 'title'),
            'redirect' => $this->properties->getLocalizedWidgetProperty($this->locale, 'redirect'),
        ];

        // Don't render the widget if required properties were not set.
        if (in_array(null, $properties)) {
            return;
        }

        // Build the example form.
        $form = $this->buildForm($exampleForm);

        // Handle the example form as soon as possible.
        if ($form->isSubmitted()) {
            try {
                $form->validate();

                // Get the example entry and save it. Any more logic while saving should be handled by the model,
                // or via event listeners, not by this widget. See ExampleModel for more info.
                $example = $form->getData();
                $this->exampleModel->save($example);

                // Redirect after the form is submitted.
                $this->response->setRedirect($this->getUrl("cms.front.site.{$properties['redirect']}.{$this->locale}"));

                return;
            } catch (ValidationException $e) {
                $this->setValidationException($e, $form);
            }
        }

        // Add any optional properties.
        $properties += [
            'image' => $this->assetModel->getById($this->properties->getLocalizedWidgetProperty($this->locale, 'image')),
        ];

        // Render the view
        $this->setTemplateView($this->getTemplate(self::TEMPLATE_DEFAULT), $properties);
    }

    /**
     * Inject the properties form component instead of building the form manually.
     *
     * @param ExamplePropertiesForm $examplePropertiesForm
     *
     * @return bool
     */
    public function propertiesAction(ExamplePropertiesForm $examplePropertiesForm) {
        // Set all available templates into the properties form component.
        $examplePropertiesForm->setTemplates($this->getAvailableTemplates(self::TEMPLATE_NAMESPACE));

        // Manually set the nodes intro the properties form component.
        $examplePropertiesForm->setNodes($this->getNodeList($this->nodeModel));

        // Build the form and set the default data.
        $form = $this->buildForm($examplePropertiesForm, [
            'title'    => $this->properties->getLocalizedWidgetProperty($this->locale, 'title'),
            'image'    => $this->properties->getLocalizedWidgetProperty($this->locale, 'image'),
            'template' => $this->getTemplate(self::TEMPLATE_DEFAULT),
            'redirect' => $this->properties->getLocalizedWidgetProperty($this->locale, 'redirect'),
        ]);

        // Handle the form submit as soon as possible.
        if ($form->isSubmitted()) {
            try {
                $form->validate();
                $data = $form->getData();

                // Set localized widget properties.
                $this->properties->setLocalizedWidgetProperty($this->locale, 'title', $data['title']);
                $this->properties->setLocalizedWidgetProperty($this->locale, 'redirect', $data['redirect']);

                // Get the asset ID, if set; null otherwise.
                $assetId = $data['image'] ? $data['image']->getId() : null;
                $this->properties->setLocalizedWidgetProperty($this->locale, 'image', $assetId);

                // Set the template.
                $this->setTemplate($data['template']);

                return true;
            } catch (ValidationException $e) {
                $this->setValidationException($e, $form);
            }
        }

        // Render the properties template
        $this->setTemplateView('cms/widget/form.properties', [
            'form' => $form->getView(),
        ]);

        return false;
    }

}
