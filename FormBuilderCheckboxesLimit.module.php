<?php namespace ProcessWire;

class FormBuilderCheckboxesLimit extends WireData implements Module
{
    public static function getModuleInfo()
    {
        return array(
          'title' => 'FormBuilder Checkboxes Limit Validation',
          'summary' => 'Validate a max/min number of checkboxes selected in Checkboxes fields in the context of FormBuilder.',
          'version' => 0.0.2,
          'autoload' => true
        );
    }

    protected $default_min_checkboxes_error = "Please select at least %d options.";
    protected $default_max_checkboxes_error = "Please select a maximum of %d options.";
    protected $min_checkboxes_error;
    protected $max_checkboxes_error;

    public function ready()
    {
        if ($this->wire()->page->process == 'ProcessFormBuilder') {
            $this->addHookAfter('InputfieldCheckboxes::getConfigInputfields', function ($e) {
                $inputfield = $e->object;
                $inputfields = $e->return;

                $field = $this->modules->get('InputfieldInteger');
                $field->label = $this->_('Minimum number of checkboxes?');
                $field->attr('name', 'min_checkboxes');
                $field->attr('value', $inputfield->min_checkboxes ?: 0);
                $inputfields->append($field);
                $inputfields->add($field);

                $field = $this->modules->get('InputfieldInteger');
                $field->label = $this->_('Maximum number of checkboxes?');
                $field->attr('name', 'max_checkboxes');
                $field->attr('value', $inputfield->min_checkboxes ?: 0);
                $inputfields->append($field);
                $inputfields->add($field);

                $field = $this->modules->get('InputfieldText');
                $field->label = $this->_('Minimum number of checkboxes error message');
                $field->attr('placeholder', $this->default_min_checkboxes_error);
                $field->attr('name', 'min_checkboxes_error');
                $field->attr('value', $inputfield->min_checkboxes_error);
                $inputfields->append($field);
                $inputfields->add($field);


                $field = $this->modules->get('InputfieldText');
                $field->label = $this->_('Maximum number of checkboxes error message');
                $field->attr('placeholder', $this->default_max_checkboxes_error);
                $field->attr('name', 'max_checkboxes_error');
                $field->attr('value', $inputfield->max_checkboxes_error);
                $inputfields->append($field);
                $inputfields->add($field);

            });
        }
        $this->addHookAfter('InputfieldCheckboxes::processInput', function ($e) {
            $inputfield = $e->object;
            $value = $e->object->value();
            $min_error = $inputfield->min_checkboxes_error ?: $inputfield->default_min_checkboxes_error;
            if ($inputfield->min_checkboxes) {
                if (count($value) < $inputfield->min_checkboxes) {
                    $e->object->error(sprintf(__($min_error), $inputfield->min_checkboxes));
                }
            }

            $max_error = $inputfield->max_checkboxes_error ?: $inputfield->default_max_checkboxes_error;
            if ($inputfield->max_checkboxes) {
                if (count($value) > $inputfield->max_checkboxes) {
                    $e->object->error(sprintf(__($max_error), $inputfield->min_checkboxes));
                }
            }

        });
    }
}
