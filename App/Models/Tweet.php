<?php
    namespace App\Models;

    use MF\Model\Model;

    class Tweet extends Model {
        // attrs | DB
        private $id;
        private $id_usuario;
        private $tweet;
        private $data;

        // getters | setters
        public function __get($attr) {
            return $this->$attr;
        }

        public function __set($attr, $value) {
            $this->$attr = $value;
        }

        // Salvando dados
        public function salvar() {
            $query = '
                insert into
                    tweets(id_usuario, tweet)
                values
                    (:id_usuario, :tweet)
            ';

            $stmt = $this->db->prepare($query);

            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->bindValue(':tweet', $this->__get('tweet'));

            $stmt->execute();

            // Retornando Obj Tweet
            return $this;
        }

        // Recuperando dados de tweets
        public function getAll() {
            // left join para recuperar o nome de usuários
            // Formatando data com DATE_FORMAT()
            // Ordenando tweets pelos mais recentes
            $query = '
                select
                    t.id, t.id_usuario, u.nome, t.tweet,
                    DATE_FORMAT(t.data, "%d/%m/%Y | %H:%i") as data
                from
                    tweets as t
                left join
                    usuarios as u
                on
                    (t.id_usuario = u.id)
                where
                    t.id_usuario = :id_usuario
                order by
                    t.data desc
            ';

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));

            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }
?>