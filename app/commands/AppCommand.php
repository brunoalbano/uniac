<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AppCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'app:instalar';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Instalacao do UNIAC - Atividades Complementares.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	protected $userData = array(
		'primeiro_nome' => '',
		'sobrenome' => '',
		'login' => '',
		'senha' => '',
		'email' => ''
	);

	protected $inicializarBanco = false;

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{	
		$this->comment('');	
		$this->comment('=====================================');
		$this->comment('');
		$this->info('  Passo: 1');
		$this->comment('');
		$this->info('    Por favor siga as');
		$this->info('    instrucoes para criar');
		$this->info('    o usuario administrador.');
		$this->comment('');
		$this->comment('-------------------------------------');

		$this->askUserFirstName();
		$this->askUserLastName();
		$this->askUserEmail();
		$this->askUserLogin();
		$this->askUserPassword();

		$this->comment('');	

		// Generate the Application Encryption key
		$this->call('key:generate');

		$this->createUser();

		$this->comment('');
		$this->comment('-------------------------------------');
		$this->comment('Configuração concluida com sucesso!');
	}

	protected function createUser()
	{
		$usuario = new Usuario;

		$usuario->perfil			= Usuario::ADMINISTRADOR;
		$usuario->primeiro_nome		= $this->userData['primeiro_nome'];
		$usuario->sobrenome			= $this->userData['sobrenome'];
		$usuario->login				= $this->userData['login'];
		$usuario->email				= $this->userData['email'];
		$usuario->senha				= Hash::make($this->userData['senha']);
		$usuario->acesso_liberado	= 1;

		$usuario->save();
	}

	/**
	 * Asks the user for the first name.
	 *
	 * @return void
	 * @todo   Use the Laravel Validator
	 */
	protected function askUserFirstName()
	{
		do
		{
			$this->comment('');

			// Ask the user to input the first name
			$first_name = $this->ask('Digite o primeiro nome do administrador: ');

			// Check if the first name is valid
			if ($first_name == '')
			{
				// Return an error message
				$this->error('Valor invalido. Por favor, tente novamente.');
			}

			// Store the user first name
			$this->userData['primeiro_nome'] = $first_name;
		}
		while( ! $first_name);
	}

	/**
	 * Asks the user for the last name.
	 *
	 * @return void
	 * @todo   Use the Laravel Validator
	 */
	protected function askUserLastName()
	{
		$this->comment('');

		// Ask the user to input the last name
		$last_name = $this->ask('Digite o sobrenome: ');

		// Store the user last name
		$this->userData['sobrenome'] = $last_name;
	}

	/**
	 * Asks the user for the user email address.
	 *
	 * @return void
	 * @todo   Use the Laravel Validator
	 */
	protected function askUserEmail()
	{
		do
		{
			$this->comment('');

			// Ask the user to input the email address
			$email = $this->ask('Digite o email: ');

			// Check if email is valid
			if ($email == '')
			{
				// Return an error message
				$this->error('Valor invalido. Por favor, tente novamente.');
			}

			// Store the email address
			$this->userData['email'] = $email;
		}
		while ( ! $email);
	}

	/**
	 * Asks the user for the user password.
	 *
	 * @return void
	 * @todo   Use the Laravel Validator
	 */
	protected function askUserLogin()
	{
		do
		{
			$this->comment('');

			// Ask the user to input the user password
			$password = $this->ask('Digite um nome de usuario (Login): ');

			// Check if email is valid
			if ($password == '')
			{
				// Return an error message
				$this->error('Valor invalido. Por favor, tente novamente.');
			}

			// Store the password
			$this->userData['login'] = $password;
		}
		while( ! $password);
	}

	/**
	 * Asks the user for the user password.
	 *
	 * @return void
	 * @todo   Use the Laravel Validator
	 */
	protected function askUserPassword()
	{
		do
		{
			$this->comment('');

			// Ask the user to input the user password
			$password = $this->ask('Digite uma senha: ');

			// Check if email is valid
			if ($password == '')
			{
				// Return an error message
				$this->error('Valor invalido. Por favor, tente novamente.');
			}

			// Store the password
			$this->userData['senha'] = $password;
		}
		while( ! $password);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			//array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}