<?php namespace Uniac\Helpers;

use Atividade;
use DateTime;
use Input;

class Helpers {
    
	public static function dateDescription($date)
	{
		if (is_string($date))
			$date = date_parse($date);

		$formattest = 'd/M/Y';
		$datetest = date_format($date, $formattest);
		if ($datetest === date($formattest))
        	return 'Hoje';
    	else if ($datetest === date($formattest, strtotime("yesterday")))
        	return 'Ontem';

		$currentYear = date('Y');
		$dateYear = date_format($date, 'Y');

		$months = array('Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez');

		$resultado = date_format($date, 'd') . ' de ' . $months[date_format($date, 'm') * 1 -1];

		if ($currentYear !== $dateYear)
			$resultado .= ' de '. date_format($date, 'Y');

		return $resultado;
	}

	public static function getStatusClass($status)
	{
		switch ($status) {
			case Atividade::ACEITA:
				return 'label-success';
			
			case Atividade::RECUSADA:
				return 'label-danger';

			case Atividade::AGUARDANDO_CORRECAO:
				return 'label-warning';

			case Atividade::AGUARDANDO_AVALIACAO:
				return 'label-waiting';

			default:
				return 'label-default';
		}
	}

	public static function strToDateTime($data)
	{
		$hj = new DateTime('NOW');
		$hoje = $hj->format('Y-m-d');
		if ($data === $hoje)
			return new DateTime('NOW');
		else
			return new DateTime($data);
	}

	/*
	 * Method to strip tags globally.
	 */
	public static function globalXssClean()
	{
	    // Recursive cleaning for array [] inputs, not just strings.
	    $sanitized = static::arrayStripTags(Input::get());
	    Input::merge($sanitized);
	}
	 
	public static function arrayStripTags($array)
	{
		// Fonte: http://usman.it/xss-filter-laravel/
	    $result = array();
	 
	    foreach ($array as $key => $value) {
	        // Don't allow tags on key either, maybe useful for dynamic forms.
	        $key = strip_tags($key);
	 
	        // If the value is an array, we will just recurse back into the
	        // function to keep stripping the tags out of the array,
	        // otherwise we will set the stripped value.
	        if (is_array($value)) {
	            $result[$key] = static::arrayStripTags($value);
	        } else {
	            $result[$key] = strip_tags($value);
	        }
	    }
	 
	    return $result;
	}
}