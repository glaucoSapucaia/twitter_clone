<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

	public function index() {
		// Definindo fluxo de index (/) para erro de login (param na URL) em AuthController.php

		// Verificando se param ?login=erro existe
		$this->view->login = isset($_GET['login']) ? $_GET['login'] : '';

		$this->render('index');
	}

	public function inscreverse() {
		// Mantendo dados vazios nos campos (erro ao renderizar a pagina devido a recuperação dos 
		// dados fornecidos pelo usuario em caso de erro)
		$this->view->usuario = array(
			'nome' => '',
			'email' => '',
			'senha' => '',
		);

		// definindo fluxo para diferentes chamadas da view inscreverse
		$this->view->erroCadastro = false;

		$this->render('inscreverse');
	}

	public function registrar() {
		// debug
		// echo '<pre>';
		// 	print_r($_POST);
		// echo '</pre>';

		// Criando classe dinamica e estabelecendo conexao com DB
		$usuario = Container::getModel('Usuario');

		// Setando attrs de Usuario obj
		$usuario->__set('nome', $_POST['nome']);
		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', $_POST['senha']);

		// debug
		// echo '<pre>';
		// 	print_r($usuario);
		// echo '</pre>';

		// Validando e salvando dados no db | Usuario obj
		if($usuario->validarCadastro() && count($usuario->getUsuarioPorEmail()) == 0) {
			// Verificando se usuario ja existe

			// debug
			// echo '<pre>';
			// 	print_r($usuario->getUsuarioPorEmail());
			// echo '</pre>';

			// Se maior que 0, já existe!
			$usuario->salvar();

			// renderizando view de sucesso do cadastro
			$this->render('cadastro');

		} else {
			// Fluxo para erro no cadastro

			// mantendo informações adicionadas pelo usuario no redirecionamento da pagina
			$this->view->usuario = array(
				'nome' => $_POST['nome'],
				'email' => $_POST['email'],
				'senha' => $_POST['senha'],
			);

			// parametro que indica erros no cadastro
			$this->view->erroCadastro = true;

			$this->render('inscreverse');
		}
		
	}

}


?>