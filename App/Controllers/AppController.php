<?php
    namespace App\Controllers;

    use MF\Controller\Action;
    use MF\Model\Container;

    class AppController extends Action {
        public function timeline() {
            // Inicando sessão para recuperar valores
            session_start();

            // protegendo rota | verificando dados da super global $_SESSION
            if (!empty($_SESSION['id']) && !empty($_SESSION['nome'])) {
                // debug
                // echo 'timeline ON!';
                // echo '<pre>';
                //     print_r($_SESSION);
                // echo '</pre>';

                // renderizando pagina de usuarios autenticados
                $this->render('timeline');

            } else {
                // redirecionando usuario nao autenticado
                header('Location: /?login=erro');
            }


        }
    }
?>