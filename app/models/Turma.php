<?php

class Turma extends BaseEloquent {

    public static function obterRegrasDeValidacao()
    {
        return array(
            'nome'         => 'required|max:50|unique_ex:turma',
            'curso_codigo' => 'required|numeric',
            'ativa' 	   => 'in:0,1',
            'matriculas'   => 'matriculas|matriculas_repetidas'
        );
    }

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'turma';
    
    public function curso()
    {
        return $this->belongsTo('Curso', 'curso_codigo');
    }

    public function matriculas()
    {
        return $this->hasMany('Matricula', 'turma_codigo');
    }

    public static function getForGroupedSelect()
    {
        $query = DB::table('turma')
                    ->select(DB::raw('turma.codigo as turma_codigo, turma.nome as turma_nome, CONCAT(curso.nome, \' - \', campus.nome) as curso_nome'))
                    ->join('curso', 'curso.codigo', '=', 'turma.curso_codigo')
                    ->join('campus', 'campus.codigo', '=', 'curso.campus_codigo')
                    ->where('ativa', true);

        if (Auth::user()->supervisor)
            $query
                ->join('usuario_curso', 'usuario_curso.curso_codigo', '=', 'turma.curso_codigo')
                ->where('usuario_curso.usuario_codigo', Auth::user()->codigo);

        return $query
                    ->orderBy('curso.nome')
                    ->orderBy('turma.nome')
                    ->get();
    }

    public static function getForGroupedSelectForCoordenador()
    {
        $query = DB::table('turma')
                    ->select(DB::raw('turma.codigo as turma_codigo, turma.nome as turma_nome, CONCAT(curso.nome, \' - \', campus.nome) as curso_nome'))
                    ->join('curso', 'curso.codigo', '=', 'turma.curso_codigo')
                    ->join('campus', 'campus.codigo', '=', 'curso.campus_codigo');

        if (Auth::user()->supervisor)
            $query
                ->join('usuario_curso', 'usuario_curso.curso_codigo', '=', 'turma.curso_codigo')
                ->where('usuario_curso.usuario_codigo', Auth::user()->codigo)
                ->where('coordenador', true);

        return $query
                    ->orderBy('curso.nome')
                    ->orderBy('turma.nome')
                    ->get();
    }
}