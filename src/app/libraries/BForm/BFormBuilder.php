<?php namespace Uniac\BForm;

use Session;
use Form;
use Illuminate\Html;

class BFormBuilder {

    private $readonly = FALSE;

    private $autofocus = FALSE;

    public function formGroup($name, $label, $input)
    {
		$class = 'form-group';

		$errors = $this->getErrors();

		if (isset($errors) && $errors->has($name))
			$class .= ' has-error';

        if (is_string($label) && empty($label) === false)
		    $label = $this->label($name, $label, array());

		$errors = $this->errors($name, $errors);

		$resultado = 
			"<div class=\"$class\">
				$label
				$input
				$errors
			</div>";

    	return $resultado;
    }

    private function errors($name, $errors)
    {
    	if (empty($errors) === false)
    		return $errors->first($name, '<span class="help-block">:message</span>');
    	else
    		return '';
    }

    public function label($name, $text = null, array $options = array())
    {
		$options = $this->prepareLabelClass($name, $options);

    	return Form::label($name, $text, $options);
    }

    public function text($name, $label, $value = null, array $options = array())
    {
		$options = $this->prepareInputOptions($name, $options);

    	return $this->formGroup($name, $label, Form::text($name, $value, $options));
    }

    public function textarea($name, $label, $value = null, array $options = array())
    {
        $options = $this->prepareInputOptions($name, $options);

        return $this->formGroup($name, $label, Form::textarea($name, $value, $options));
    }

    public function email($name, $label, $value = null, $options = array())
    {
		$options = $this->prepareInputOptions($name, $options);

    	return $this->formGroup($name, $label, Form::email($name, $value, $options));
    }

    public function integer($name, $label, $value = null, $options = array())
    {
        $options = $this->prepareInputOptions($name, $options);

        if (isset($options['step']) === false)
            $options['step'] = 1;

        return $this->formGroup($name, $label, Form::input("number", $name, $value, $options));        
    }

    public function password($name, $label, array $options = array())
    {
		$options = $this->prepareInputOptions($name, $options);

    	return $this->formGroup($name, $label, Form::password($name, $options));
    }

    public function hidden($name, $value = null, $options = array())
    {
        $options = $this->prepareInputOptions($name, $options);
        
        return Form::hidden($name, $value, $options);
    }

    public function checkbox($name, $label, $value = 1, $checked = null, array $options = array())
    {
        $options = $this->prepareInputOptions($name, $options, FALSE);

    	$checkbox = Form::checkbox($name, $value, $checked, $options);

        $title = '';
        if (isset($options) && isset($options['title']))
            $title = $options['title'];

		$resultado =
			"<div class=\"checkbox\" title=\"$title\">
		      <label>
		        $checkbox $label
		      </label>
		    </div>";

		return $resultado;
    }

    public function select($name, $label, $list = array(), $selected = null, array $options = array())
    {
		$options = $this->prepareInputOptions($name, $options);
		
    	return $this->formGroup($name, $label, Form::select($name, $list, $selected, $options));
    }

    public function file($name, $label, $options = array())
    {
        $options = $this->prepareInputOptions($name, $options);

        return $this->formGroup($name, $label, Form::file($name, $options));
    }

    public function date($name, $label, $value = null, $options = array())
    {
        $options = $this->prepareInputOptions($name, $options);

        return $this->formGroup($name, $label, Form::input('date', $name, $value, $options));
    }

    public function radio($name, $label, $value = null, $checked = null, $options = array())
    {        
        $options = $this->prepareInputOptions($name, $options, FALSE);

        $radio = Form::radio($name, $value, $checked, $options);

        $title = '';
        if (isset($options) && isset($options['title']))
            $title = $options['title'];

        $resultado =
            "<div class=\"radio\" title=\"$title\">
              <label>
                $radio $label
              </label>
            </div>";

        return $resultado;
    }

    public function model($model, array $options = array(), $readonly = FALSE)
    {
        if (isset($options['autocomplete']) === false)
            $options['autocomplete'] = "off";

        $this->readonly = $readonly;

        return Form::model($model, $options);
    }

    public function open(array $options = array())
    {
        if (isset($options['autocomplete']) === false)
            $options['autocomplete'] = "off";

        return Form::open($options);
    }

    public function close()
    {
        $this->readonly = FALSE;
        $this->autofocus = FALSE;
        return Form::close();
    }

    public function submit($value = null, $options = array())
    {
        if ($this->readonly === FALSE)
        {
            $options['class'] = 'btn btn-default';
            return Form::submit($value, $options);
        }
    }

    public function errorsAlert()
    {
    	$errors = $this->getErrors();

    	if (empty($errors) === false)
    	return '<div class="alert alert-danger"><strong>Atenção!</strong> Corrija os erros para continuar.</div>';
    }

    private function prepareInputClass(array $options)
    {
		$class = 'form-control';

    	if (array_key_exists('class', $options))
    		$class .= ' ' . $options['class'];

    	$options['class'] = $class;

    	return $options;
    }

    private function prepareInputReadonly(array $options)
    {
        if ($this->readonly === TRUE)
            $options['disabled'] = 'disabled';

        return $options;
    }

    private function prepareInputOptions($id, array $options, $includeFormClass = TRUE)
    {
        if ($includeFormClass)
            $options = $this->prepareInputClass($options);

        $options = $this->prepareInputReadonly($options);

        $options['id'] = $id;

        return $options;
    }

    private function prepareLabelClass($name, array $options)
    {
		$class = 'control-label';

    	if (array_key_exists('class', $options))
    		$class .= ' ' . $options['class'];

    	$options['class'] = $class;

    	return $options;
    }

    private function getErrors()
    {
    	return Session::get("errors");
    }
}

