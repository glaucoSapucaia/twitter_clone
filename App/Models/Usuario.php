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

        // Autenticando usuario
        public function autenticar() {
            $query = '
                select
                    id, nome, email
                from
                    usuarios
                where
                    email = :email and senha = :senha
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':senha', $this->__get('senha'));

            $stmt->execute();

            // COmo deve haver apena sum usuário válido, não usamos fetchAll, queremos apenas o primeiro usuario retornado
            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Fluxo de autenticação | verificando se dados existem no DB
            if(!empty($usuario['id']) && !empty($usuario['nome'])) {
                // recuperando dados encontrados
                $this->__set('id', $usuario['id']);
                $this->__set('nome', $usuario['nome']);
            }

            // Retornando objeto de dados recuperados do DB após autenticação
            return $this;
        }

        // buscando usuarios por pesquisa
        public function getAll() {
            // like -> retorna semelhença entre strings (nomes)
            $query = '
                select
                    id, nome, email
                from
                    usuarios
                where
                    nome like :nome and id != :id_usuario
            ';

            $stmt = $this->db->prepare($query);

            // Adicione o % para a busca livre da combinação de letras
            $stmt->bindValue(':nome', '%' . $this->__get('nome') . '%');

            // evitando busca pelo proprio usuario logado
            $stmt->bindValue(':id_usuario', $this->__get('id'));

            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }
?>