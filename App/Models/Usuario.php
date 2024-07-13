<?php
    namespace App\Models;

    // model abstrato | MF
    use MF\Model\Model;

    class Usuario extends Model {
        // Attrs | Colunas DB
        private $id;
        private $nome;
        private $email;
        private $senha;

        // getters | setters
        public function __get($attr) {
            return $this->$attr;
        }

        public function __set($attr, $value) {
            $this->$attr = $value;
            // return $this;
        }

        // Salvando usuário
        public function salvar() {
            // query
            $query = '
                insert into
                    usuarios(nome, email, senha)
                    values(:nome, :email, :senha)
            ';

            // recuperand PDO obj de MF\Model - statement
            $stmt = $this->db->prepare($query);
            
            // binds
            $stmt->bindValue(':nome', $this->__get('nome'));
            $stmt->bindValue(':email', $this->__get('email'));

            // criptografia | md5 -> hash 32 caracteres
            $stmt->bindValue(':senha', $this->__get('senha'));

            $stmt->execute();

            return $this;
        }

        // validando dados de cadastro
        public function validarCadastro() {
            // flag para validação
            $valido = true;

            // Verificando se campos possuem pelo menos 3 caracteres
            if(strlen($this->__get('nome')) < 3) {
                $valido = false;
            }

            if(strlen($this->__get('email')) < 3) {
                $valido = false;
            }

            if(strlen($this->__get('senha')) < 3) {
                $valido = false;
            }

            return $valido;
        }

        // resgatando dados para não permitir registros duplicados
        public function getUsuarioPorEmail() {
            $query = '
                select
                    nome, email
                from
                    usuarios
                where
                    email = :email
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->execute();

            // FETCH_ASSOC -> Retorna um array() associativo
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }
?>