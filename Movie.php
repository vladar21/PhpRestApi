<?php

require_once 'Db.php';

class Movie {

    /**
     * @var int $id primary key
    */
    public $id;
    /**
     * @var varchar(100) $title название фильма
     */
    public $title;
    /**
     * @var varchar(100) $director режиссер
     */
    public $director;
    /**
     * @var int $year год выпуска
     */
    public $year;
    /**
     * @var int $like фильм понравился
     */
    public $like;
    /**
     * @var int $dislike фильм не понравился
     */
    public $dislike;
    /**
     * @var varchar(300) $poster путь к картинке
     */
    public $poster;

    public static function getAll(){
        $db = new Db();
        $movies = $db->query("SELECT id, title, director, year, liked as 'like', dislike, poster, (liked - dislike) AS rating FROM movies ORDER BY rating desc")
        ->resultset();

        return $movies;
    }

    public static function getById($id){
        $db = new Db();
        $movie = $db->query("SELECT id, title, director, year, liked as 'like', dislike, poster, (liked - dislike) AS rating FROM movies WHERE id = ? LIMIT ?")
        ->bind(1, $id)
        ->bind(2, 1)
        ->single();

        return $movie;
    }

    public function saveNew(){
        $db = new Db();
        $result = $db->query('INSERT INTO movies(title, director, year, liked, dislike, poster) VALUES(?, ?, ?, ?, ?, ?)')
        ->bind(1, $this->title)
        ->bind(2, $this->director)
        ->bind(3, $this->year)
        ->bind(4, $this->like)
        ->bind(5, $this->dislike)
        ->bind(6, $this->poster)
        ->execute();

        return $result;
    }

    public function updateMovie(){
        $db = new Db();
        $result = $db->query('UPDATE movies SET title = ?, director = ?, year = ?, liked = ?, dislike = ?, poster = ? WHERE id = ?')
        ->bind(1, $this->title)
        ->bind(2, $this->director)
        ->bind(3, $this->year)
        ->bind(4, $this->like)
        ->bind(5, $this->dislike)
        ->bind(6, $this->poster)
        ->bind(7, $this->id)
        ->execute();

        return $result;
    }

    public function deleteById(){
        $db = new Db();
        $result = $db->query('DELETE FROM movies WHERE id=?')
        ->bind(1, $this->id)
        ->execute();

        return $result;
    }


}
