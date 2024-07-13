<?php
    namespace App\Controllers;

    use MF\Controller\Action;
    use MF\Model\Container;

    class AuthController extends Action {

        public function autenticar() {
            // debug
            // echo 'Autenticar ON';
            // echo '<pre>';
            //     print_r($_POST);
            // echo '</pre>';

            // Instanciando usuario | Container.php
            $usuario = Container::getModel('Usuario');

            // Setando attrs
            $usuario->__set('email', $_POST['email']);

            // Convertendo senha para hash | verificnado com hash do DB
            // md5() -> retorna um hash de 32 caracteres
            $usuario->__set('senha', md5($_POST['senha']));

            // debug
            // echo '<pre>';
            //     print_r($usuario);
            // echo '</pre>';

            // Autenticando usuario | Model Usuario.php
            $usuario->autenticar();

            // debug
            // echo '<pre>';
            //     print_r($retorno);
            // echo '</pre>';

            // debug
            // echo '<pre>';
            //     print_r($usuario);
            // echo '</pre>';

            // Verificando autenticação | existencia dos dados
            if(!empty($usuario->__get('id')) && !empty($usuario->__get('nome'))) {
                // debug
                // echo 'Usuario Autenticado!';

                // manipulando seçôes (usuario autenticado)
                session_start();

                $_SESSION['id'] = $usuario->__get('id');
                $_SESSION['nome'] = $usuario->__get('nome');

                // Redirecionamento para página PROTEGIDA
                header('Location: /timeline');

            } else {
                // debug
                // echo 'Erro de autenticação';

                // Redirecionando para index (+ msg de erro)
                // Adicionando param de erro de login a URL
                header('Location: /?login=erro');
            }
        }

        // finalizando sessão | route sair
        public function sair() {
            // manipulando sessão
            session_start();
            session_destroy();

            // redirecionando usuario  para index | clique no link sair da view timeline.phtml
            header('Location: /');
        }
    }

?>