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
    }
?>