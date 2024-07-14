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

        public function removerTweet() {
            $this->validaAutenticacao();

            // debug
            // echo '<pre>';
            //     print_r($_GET);
            // echo '</pre>';

            $tweet = Container::getModel('Tweet');
            $tweet->__set('id', $_GET['id_tweet']);

            $tweet->remover();

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

        public function quemSeguir() {
            // validando usuario
            $this->validaAutenticacao();

            // debug
            // echo "Quem seguir ON!";
            // echo '<pre>';
            //     print_r($_GET);
            // echo '</pre>';

            // param recebido da busca
            $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

            // flag para usuários
            $usuarios = array();

            // debug
            // echo '<pre>';
            //     print_r($_SESSION);
            // echo '</pre>';

            // fluxo para pesquisa
            if ($pesquisarPor != '') {
                // instanciando obj usuario para busca por nomes
                $usuario = Container::getModel('Usuario');
                
                // setando valor da busca
                $usuario->__set('nome', $pesquisarPor);

                // setando id de usuario autenticado para evitar busca por ele mesmo
                $usuario->__set('id', $_SESSION['id']);

                $usuarios = $usuario->getAll();

                // debug
                // echo '<pre>';
                //     print_r($usuarios);
                // echo '</pre>';
            }

            // usuarios dinamicos para a view quemSeguir
            $this->view->usuarios = $usuarios;

            // renderizando página
            $this->render('quemSeguir');
        }

        // action de acao | seguir e deixar de seguir
        public function acao() {
            // validando usuario
            $this->validaAutenticacao();

            // debug
            // echo '<pre>';
            //     print_r($_GET);
            // echo '</pre>';

            // definindo flags para fluxo da pagina
            $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
            $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

            // instanciando classe usuario
            $usuario = Container::getModel('Usuario');

            // Setando attrs
            $usuario->__set('id', $_SESSION['id']);

            if ($acao == 'seguir') {
                $usuario->seguirUsuario($id_usuario_seguindo);

            } else if ($acao == 'deixar_de_seguir') {
                $usuario->deixarSeguirUsuario($id_usuario_seguindo);
            }

            // dedirecionando usuario após click em btn desguir | deixar de seguir
            header('Location: /quem_seguir');
        }
    }
?>