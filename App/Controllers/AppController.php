<?php
    namespace App\Controllers;

    use MF\Controller\Action;
    use MF\Model\Container;

    class AppController extends Action {
        // Actions AUTENTICADAS
        public function timeline() {
            // Validando dados e iniciando sessão
            $this->validaAutenticacao();

            // debug
            // echo 'timeline ON!';
            // echo '<pre>';
            //     print_r($_SESSION);
            // echo '</pre>';

            // Recuperando lista de tweets do DB
            $tweet = Container::getModel('Tweet');

            // flag para recuperar apenas tweets do usuario logado
            $tweet->__set('id_usuario', $_SESSION['id']);

            $tweets = $tweet->getAll();

            // debug
            // echo '<pre>';
            //     print_r($tweets);
            // echo '</pre>';

            // definindo params da view
            $this->view->tweets = $tweets;

            // renderizando pagina de usuarios autenticados
            $this->render('timeline');

        }

        public function tweet() {
            // Validando dados e iniciando sessão
            $this->validaAutenticacao();

            // debug
            // echo '<pre>';
            //     print_r($_POST);
            // echo '</pre>';

            // Instanciando Obj Tweet de Models
            $tweet = Container::getModel('Tweet');

            // Setando attrs
            $tweet->__set('tweet', $_POST['tweet']);
            $tweet->__set('id_usuario', $_SESSION['id']);

            // Salvando tweet no DB
            $tweet->salvar();

            // Redirecionando usuario
            header('Location: /timeline');

        }

        public function validaAutenticacao() {
            // Inicando sessão para recuperar valores
            session_start();

            // protegendo rota | verificando dados da super global $_SESSION
            if (!isset($_SESSION['id']) || empty($_SESSION['id']) || !isset($_SESSION['nome']) || empty($_SESSION['nome'])) {
                // redirecionando usuario nao autenticado
                header('Location: /?login=erro');
            }
        }
    }
?>