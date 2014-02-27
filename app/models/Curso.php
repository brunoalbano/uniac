<?php

class Curso extends BaseEloquent {

    public static function obterRegrasDeValidacao()
    {
        return array(
            'nome'                  => 'required|max:50|unique_ex:curso',
            'campus_codigo'         => 'required|numeric',
            'tipos_de_atividade'    => 'tipos_de_atividade',
            'matrizes_curriculares' => 'matrizes_curriculares'
        );
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'curso';

    public function supervisores()
    {
        return $this->belongsToMany('Usuario', 'usuario_curso', 'curso_codigo', 'usuario_codigo');
    }
    
    public function campus()
    {
        return $this->belongsTo('Campus', 'campus_codigo');
    }

    public function tipos_de_atividade()
    {
    	return $this->hasMany('TipoAtividade', 'curso_codigo');
    }

    public function matrizes_curriculares()
    {
        return $this->hasMany('MatrizCurricular', 'curso_codigo');
    }

    public function anexos()
    {
        return $this->belongsToMany('Anexo', 'curso_anexo', 'curso_codigo', 'anexo_codigo');
    }
}