<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class GridQueryBuilder
{
	private $input;

	private $query;

	private $columns;

	public function __construct($table, array $columns)
	{
		$this->query = DB::table($table);
		$this->columns = $columns;
	}

	public function toGrid($input)
	{
		$this->input = $input;

		$this->montarCondicoes();

		$total = $this->query->count();

		$this->montarColunas();
		$this->montarOrdenacao();
		$this->montarPaginacao();

		$data = $this->query->get();

		$resultado = array('total' => $total, 'data' => $data);

		return $resultado;
	}
	
	protected function montarColunas()
	{
		$rawSelect = '';
		foreach ($this->columns as $column) {
			if (empty($rawSelect) === false)
				$rawSelect .= ', ';

			$rawSelect .= $column . ' as "' . $column . '"';
		}

		$this->query->select(DB::raw($rawSelect));
	}

	protected function montarCondicoes()
	{
		if ($this->input === null || !isset($this->input['filterscount']) || $this->input['filterscount'] <= 0)
			return;
		
		$filterscount = $this->input['filterscount'];
				
		$where = ' (';
		$tmpdatafield = '';
		$filteroperator = '';
		$values = array();

		for ($i=0; $i < $filterscount; $i++)
	    {
			// get the filter's value.
			$filtervalue = $this->input['filtervalue' . $i];
			// get the filter's condition.
			$filtercondition = $this->input['filtercondition' . $i];
			// get the filter's column.
			$filterdatafield = $this->input['filterdatafield' . $i];
			
			if (empty($filterdatafield) || !$this->campoValidoParaConsulta($filterdatafield))
			{
				throw new Exception("Campo inválido para filtro: " . $filterdatafield);				
			}

			$filtervalue =  $this->mapearValorFiltro($filterdatafield, $filtercondition, $filtervalue);

			if (empty($tmpdatafield) === false)
			{
				if ($tmpdatafield !== $filterdatafield)
				{
					$where .= ') AND (';
				}
				else 
					if ($tmpdatafield === $filterdatafield)
					{
						if ((int)$filteroperator === 0)
							$where .= ' AND ';
						else 
							$where .= ' OR ';
					}
			}

			switch($filtercondition)
			{
				case "CONTAINS":					
					$filtervalue = '%' . $filtervalue . '%';
					$where .= " $filterdatafield LIKE ?";
					break;
				case "DOES_NOT_CONTAIN":
					$filtervalue = '%' . $filtervalue . '%';
					$where .= " $filterdatafield NOT LIKE ?";
					break;
				case "EQUAL":
					$where .= " $filterdatafield = ?";
					break;
				case "NOT_EQUAL":
					$where .= " $filterdatafield <> ?";
					break;
				case "GREATER_THAN":
					$where .= " $filterdatafield > ?";
					break;
				case "LESS_THAN":
					$where .= " $filterdatafield < ?";
					break;
				case "GREATER_THAN_OR_EQUAL":
					$where .= " $filterdatafield >= ?";
					break;
				case "LESS_THAN_OR_EQUAL":
					$where .= " $filterdatafield <= ?";
					break;
				case "STARTS_WITH":
					$filtervalue = $filtervalue . '%';
					$where .= " $filterdatafield LIKE ?";
					break;
				case "ENDS_WITH":
					$filtervalue = '%' . $filtervalue;
					$where .= " $filterdatafield LIKE ?";
					break;
			}

			$values[] = $filtervalue;
			
			$filteroperator = $this->input['filteroperator' . $i];
			$tmpdatafield = $filterdatafield;			
		}

		$where .= ') ';
		
		$this->query->whereRaw($where, $values);
	}

	protected function montarOrdenacao()
	{
		if (!isset($this->input['sortdatafield']))
			return;
		
		$sortfield = $this->input['sortdatafield'];
		$sortorder = $this->input['sortorder'];

		if (!$this->campoValidoParaConsulta($sortfield))
			return;

		//dd($sortfield . ' ' . $sortorder);
		$this->query->orderBy($sortfield, $sortorder);
	}

	protected function montarPaginacao()
	{
		if (!isset($this->input['pagenum']) && !isset($this->input['pagesize']))
			return;

		$pagenum = $this->input['pagenum'];

		$pagesize = $this->input['pagesize'];
		$start = $pagenum * $pagesize;

		$this->query->skip($start)->take($pagesize);
	}

	protected function campoValidoParaConsulta($campo)
	{
		if (empty($this->columns) === false)
			if(in_array($campo, $this->columns) === false)
				return false;

		return true;
	}

	protected function mapearValorFiltro($campo, $condicao, $valor)
	{
		$valorMapeado = $valor;

		if (strtolower($valor) === 'true' || $valor === true)
			$valorMapeado = 1;
		else
			if (strtolower($valor) === 'false' || $valor === false)
				$valorMapeado = 0;

		return $valorMapeado;
	}

	/**
	 * Dynamically handle calls into the query instance.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		if (method_exists($this, $method))
		{
			array_unshift($parameters, $this);

			$result = call_user_func_array(array($method), $parameters);
		}
		else
		{
			$result = call_user_func_array(array($this->query, $method), $parameters);
		}

		if (empty($result) === false)
			if ($result === $this->query)
				return $this;
			else
				return $result;
	}
}

class BaseEloquent extends Eloquent {

	protected $primaryKey = 'codigo';

	public $timestamps = false;

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'excluir_em';

    public static function boot()
    {
        parent::boot();

        // Setup event bindings...
        self::updated(function($model) {
        	LogHandler::atualizado($model);
        });

        self::created(function($model) {
        	LogHandler::criado($model);
        });

        self::deleted(function($model) {
        	LogHandler::excluido($model);
        });
    }

    public static function selectToGrid($columns = array())
    {
    	$instance = new static;

    	if (is_array($columns) === false)
    		$columns =  func_get_args();

    	return new GridQueryBuilder($instance->getTable(), $columns);
    }
}