<?php


require_once 'Api.php';
require_once 'Movie.php';

class MovieApi extends Api
{
    public $apiName = 'movies';

    /**
     * Метод GET
     * Вывод списка всех записей
     * http://ДОМЕН/api/movies
     * @return string
     */
    public function all()
    {
        $movies = Movie::getAll();
        if ($movies) {
            return $this->response($movies, 200);
        }
        return $this->response(["message" => 'Data not found'], 404);
    }

    /**
     * Метод GET
     * Просмотр отдельной записи (по id)
     * http://ДОМЕН/api/movies/1
     * @return string
     */
    public function view()
    {
        //id должен быть первым параметром после /movies/x
        $id = array_shift($this->requestUri);

        if ($id) {
            $movie = [];
            array_push($movie, Movie::getById($id));
            if ($movie) {
                return $this->response($movie, 200);
            }
        }
        return $this->response(["message" => 'Data not found'], 404);
    }

    /**
     * Метод POST
     * Создание новой записи
     * http://ДОМЕН/api/movies + параметры запроса (значенич полей нового объекта класса Movie)
     * @return string
     */
    public function add()
    {
        $title = $this->requestParams['title'] ?? '';
        $director = $this->requestParams['director'] ?? '';
        $year = $this->requestParams['year'] ?? 0;
        $like = $this->requestParams['like'] ?? 0;
        $dislike = $this->requestParams['dislike'] ?? 0;
        if (is_uploaded_file($_FILES['poster']['tmp_name'])) {
            $path = "images/".$_FILES['poster']['name'];
            move_uploaded_file($_FILES['poster']['tmp_name'], $path);
            $poster = 'http://'.$_SERVER['HTTP_HOST'].'/images/'.$_FILES['poster']['name'];
        }
        else{

            $poster = '';
        }

        if ($title) {
            $movie = new Movie();
            $movie->title = $title;
            $movie->director = $director;
            $movie->year = $year;
            $movie->like = $like;
            $movie->dislike = $dislike;
            $movie->poster = $poster;
            if ($movie = $movie->saveNew()) {
                return $this->response(["message" => "New record added"], 200);
            }
        }
        return $this->response(["message" => "Saving error"], 500);
    }

    /**
     * Метод POST
     * Обновление отдельной записи (по ее id)
     * http://ДОМЕН/api/movies/update/1 + параметры запроса обновляемог объекта класса Movie
     * @return string
     */
    public function update()
    {
        $parse_url = parse_url($this->requestUri[1]);
        $movieId = ($parse_url['path'] * 1) ?? null;
        //$movieId = $this->requestParams['id'];

        if (isset($movieId)){
            $movie = Movie::getById($movieId);

            if (!$movie) {
                return $this->response(["message" => "Movie with id=$movieId not found"], 404);
            } else {
                $movie->title = $this->requestParams['title'] ?? $movie->title;
                $movie->director = $this->requestParams['director'] ?? $movie->director;
                $movie->year = $this->requestParams['year'] ?? $movie->year;
                $movie->like = $this->requestParams['like'] ?? $movie->like;
                $movie->dislike = $this->requestParams['dislike'] ?? $movie->dislike;
                if (is_uploaded_file($_FILES['poster']['tmp_name'])) {
                    $path = "images/".$_FILES['poster']['name'];
                    move_uploaded_file($_FILES['poster']['tmp_name'], $path);
                    if (file_exists($movie->poster)) { unlink($movie->poster); }
                    $movie->poster = $path;
                }

                if ($movie->updateMovie()){
                    $m = [];
                    array_push($m, $movie);
                    return $this->response($m, 200);
                }
                else {
                    return $this->response(["message" => "Error when trying to update a record in the database"], 404);
                }


            }
        }
        else{
            return $this->response(["message" => "You send Id wich is null"], 404);
        }


    }

    /**
     * Метод DELETE
     * Удаление отдельной записи (по ее id)
     * http://ДОМЕН/movies/1
     * @return string
     */
    public function delete()
    {
        $parse_url = parse_url($this->requestUri[0]);
        $movieId = $parse_url['path'] ?? null;

        $movie = Movie::getById($movieId);

        if (!$movieId || !$movie) {
            return $this->response(["message" => "Movie with id=$movieId not found"], 404);
        }
        // удаляем картинку с сервера, если она есть у этой записи
        if (file_exists($movie->poster)) { unlink($movie->poster); }
        $result = $movie->deleteById();

        if ($result) {
            return $this->response(["message" => 'Data deleted.'], 200);
        }
        return $this->response(["message" => "Delete error"], 500);
    }
}
