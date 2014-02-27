<?php

class Matricula extends BaseEloquent {
	const ATIVO = 1;
	const INATIVO = 2;
	const BLOQUEADO = 3;
	const HOMOLOGADO = 4; 

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'matricula';

    public function turma()
    {
        return $this->belongsTo('Turma', 'turma_codigo');
    }

    public function usuario()
    {
        return $this->belongsTo('Usuario', 'usuario_codigo');
    }

    public function processarHoras()
    {
        $resultado = $this->processarTipos();

        $horas_necessarias = $this->horas_necessarias;

        $horas_necessarias_obrigatorias = $this->horas_necessarias_obrigatorias;

        $horas_aceitas_normais = (int)$this->saldo_anterior;

        $horas_aceitas_obrigatorias = 0;

        foreach ($resultado as $r) {
            if (empty($r['tipo']->tipo_atividade_codigo)) {
                if ($r['tipo']->obrigatorio)
                    $horas_aceitas_obrigatorias += $r['horas_aceitas'];                
                else
                    $horas_aceitas_normais += $r['horas_aceitas'];
            }
        }

        $horas_necessarias_normais = $horas_necessarias - $horas_necessarias_obrigatorias;

        $horas_aceitas_normais = min($horas_necessarias_normais, $horas_aceitas_normais);

        $horas_aceitas_obrigatorias = min($horas_necessarias_obrigatorias, $horas_aceitas_obrigatorias);

        return $horas_aceitas_normais + $horas_aceitas_obrigatorias;
    }

    public function processarTipos()
    {
        $matricula_codigo = $this->codigo;

        $tipos = DB::table('atividade')
                    ->select(DB::raw('tipo_atividade_codigo, sum(horas_aceitas) horas_aceitas'))
                    ->where('matricula_codigo', $matricula_codigo)
                    ->groupBy('tipo_atividade_codigo')
                    ->lists('horas_aceitas', 'tipo_atividade_codigo');

        $resultado = array();

        $this->processar($tipos, $resultado, false);

        return $resultado;
    }

    private function processar($tipos, &$resultado, $possuiItens = true)
    {
        foreach ($tipos as $tipo_atividade_codigo => $horas_aceitas) {
            if (array_key_exists($tipo_atividade_codigo, $resultado) == false) {
                $tipo = TipoAtividade::find($tipo_atividade_codigo, array('tipo_atividade_codigo', 'horas', 'obrigatorio'));

                $resultado[$tipo_atividade_codigo] = array(
                    'tipo' => $tipo, 
                    'horas_aceitas' => 0, 
                    'possui_itens' => $possuiItens
                );
            }

            $tipo = $resultado[$tipo_atividade_codigo]['tipo'];

            if ($possuiItens) {
                $resultado[$tipo_atividade_codigo]['horas_aceitas'] += (int)$horas_aceitas;
                $resultado[$tipo_atividade_codigo]['horas_aceitas'] = min($resultado[$tipo_atividade_codigo]['horas_aceitas'], (int)$tipo->horas);
            }
            else
                $resultado[$tipo_atividade_codigo]['horas_aceitas'] = (int)$horas_aceitas;

            if (empty($tipo->tipo_atividade_codigo) == false)
                $this->processar(array($tipo->tipo_atividade_codigo => $horas_aceitas), $resultado);
        }
    }

    public function getHorasNecessariasAttribute()
    {
        $matrizCurricular = MatrizCurricular::find($this->matriz_curricular_codigo);
        return (int)$matrizCurricular->horas;
    }

    public function getHorasNecessariasObrigatoriasAttribute()
    {
        $horas = DB::table('tipo_atividade')
                    ->where('curso_codigo', $this->turma->curso_codigo)
                    ->where('obrigatorio', 1)
                    ->sum('horas');

        if (empty($horas))
            return 0;
        else
            return (int)$horas;
    }

    public function getHorasNecessariasNormaisAttribute()
    {
        return $this->horas_necessarias - $this->horas_necessarias_obrigatorias;
    }

    public function getHorasFaltandoAttribute()
    {
        if ((int)$this->status === self::HOMOLOGADO)
            return 0;
        else
            return max(0, $this->horas_necessarias - (int)$this->horas_aceitas);
    }    
}