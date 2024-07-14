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
            // () -> sub consulta
            // verificamos se o usuario da sessão já segue o usuario pesquisado
            // Fluxo para exibir o btn seguir e deixar de seguir em quemSeguir.phtml
            $query = '
                select
                    u.id, u.nome, u.email,
                    (
                        select
                            count(*)
                        from
                            usuarios_seguidores as us
                        where
                            us.id_usuario = :id_usuario and us.id_usuario_seguindo = u.id
                    ) as seguindo_sn
                from
                    usuarios as u
                where
                    u.nome like :nome and u.id != :id_usuario
            ';

            $stmt = $this->db->prepare($query);

            // Adicione o % para a busca livre da combinação de letras
            $stmt->bindValue(':nome', '%' . $this->__get('nome') . '%');

            // evitando busca pelo proprio usuario logado
            $stmt->bindValue(':id_usuario', $this->__get('id'));

            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        // seguir | deixar de seguir
        public function seguirUsuario($id_usuario_seguindo) {
            // debug
            // echo 'Seguir Usuario!';

            $query = '
                insert into
                    usuarios_seguidores(id_usuario, id_usuario_seguindo)
                    values(:id_usuario, :id_usuario_seguindo);
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);

            $stmt->execute();

            return true;
        }

        public function deixarSeguirUsuario($id_usuario_seguindo) {
            // debug
            // echo 'Deixa de seguir Usuario!';

            $query = '
                delete from
                    usuarios_seguidores
                where
                    id_usuario = :id_usuario and id_usuario_seguindo = :id_usuario_seguindo
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);

            $stmt->execute();

            return true;
        }

        // Dados gerais do usuario
        public function getInfoUsuario() {
            $query = '
                select
                    nome
                from
                    usuarios
                where
                    id = :id_usuario
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));

            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        public function getTotalTweets() {
            $query = '
                select
                    count(*) as total_tweets
                from
                    tweets
                where
                    id_usuario = :id_usuario
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));

            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        public function getTotalSeguindo() {
            $query = '
                select
                    count(*) as total_seguindo
                from
                    usuarios_seguidores
                where
                    id_usuario = :id_usuario
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));

            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        public function getTotalSeguidores() {
            $query = '
                select
                    count(*) as total_seguidores
                from
                    usuarios_seguidores
                where
                    id_usuario_seguindo = :id_usuario
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id'));

            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
    }
?>