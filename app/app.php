<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Book.php";
    require_once __DIR__."/../src/Author.php";

    $server = "mysql:host=localhost:8889;dbname=library";
    $username = "root";
    $password = "root";
    $DB = new PDO($server, $username, $password);

    $app = new Silex\Application();

    $app->register(new Silex\Provider\TwigServiceProvider(), array(                          'twig.path'=>__DIR__."/../views"
    ));

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    $app->get('/', function() use ($app) {
        return $app['twig']->render('index.html.twig', array('authors' => Author::getAll(), 'books' => Book::getAll()));
    });

    $app->get('/books', function() use ($app) {
        return $app['twig']->render('books.html.twig', array('books' => Book::getAll()));
    });

    $app->post("/books", function() use ($app) {
        $title = $_POST['title'];
        $book = new Book($_POST['title']);
        $book->save();
        return $app['twig']->render('books.html.twig', array('books' => Book::getAll()));
    });

    $app->post("/delete_books", function() use ($app) {
        Book::deleteAll();
        return $app['twig']->render('index.html.twig');
    });

    $app->get("/books/{id}", function($id) use ($app) {
       $book = Book::find($id);
       return $app['twig']->render('book.html.twig', array('book' => $book, 'authors' => $book->getAuthors(), 'all_authors' => Author::getAll()));
   });

    $app->post("/add_authors", function() use($app){
        $author = Author::find($_POST['author_id']);
        $book = Book::find($_POST['book_id']);
        $book->addAuthor($author);
        return $app['twig']->render('book.html.twig', array('book' => $book, 'books' => Book::getAll(), 'authors' => $book->getAuthors(), 'all_authors' => Author::getAll()));
    });

    $app->get("/books/{id}/edit", function($id) use ($app) {
        $book = Book::find($id);
        return $app['twig']->render('book_edit.html.twig', array('book' => $book));
    });

    $app->patch("/books/{id}", function($id) use ($app) {
        $title = $_POST['title'];
        $book = Book::find($id);
        $book->update($title);
        return $app['twig']->render('book.html.twig', array('book' => $book, 'authors' => $book->getAuthors(), 'all_authors' => Author::getAll()));
    });

    $app->delete("/books/{id}", function($id) use ($app) {
        $book = Book::find($id);
        $book->delete();
        return $app['twig']->render('index.html.twig', array('books' => Book::getAll()));
    });

    $app->get('/search_books', function() use ($app) {
        $books = Book::getAll();
        $search = strtolower($_GET['search']);
        $books_matching_search = array();
        foreach($books as $book) {
            if (strpos(strtolower($book->getTitle()), $search) !== false) {
                array_push($books_matching_search, $book);
            }
        }
        return $app['twig']->render('search_books.html.twig', array('books' => $books_matching_search));
    });

////////////////////////////////////////////////

    $app->get('/authors', function() use ($app) {
        return $app['twig']->render('authors.html.twig', array('authors' => Author::getAll()));
    });

    $app->post("/authors", function() use ($app) {
        $name = $_POST['name'];
        $id = $_POST['id'];
        $author = new Author($name, $id);
        $author->save();
        return $app['twig']->render('authors.html.twig', array('authors' => Author::getAll()));
    });

    $app->post("/delete_authors", function() use ($app) {
        Author::deleteAll();
        return $app['twig']->render('index.html.twig');
    });

    $app->get("/authors/{id}", function($id) use ($app) {
       $author = Author::find($id);
       return $app['twig']->render('author.html.twig', array('author' => $author, 'books' => $author->getBooks(), 'all_books' => Book::getAll()));
   });

   $app->post("/add_books", function() use ($app){
       $book = Book::find($_POST['book_id']);
       $author = Author::find($_POST['author_id']);
       $author->addBook($book);
       return $app['twig']->render('author.html.twig', array('author' => $author, 'authors' => Author::getAll(), 'books' => $author->getBooks(), 'all_books' => Book::getAll()));
   });

    $app->get("/authors/{id}/edit", function($id) use ($app) {
        $author = Author::find($id);
        return $app['twig']->render('author_edit.html.twig', array('author' => $author));
    });

    $app->patch("/authors/{id}", function($id) use ($app) {
        $name = $_POST['name'];
        $author = Author::find($id);
        $author->update($name);
        return $app['twig']->render('author.html.twig', array('author' => $author, 'books' => $author->getBooks(), 'all_books' => Book::getAll()));
    });

    $app->delete("/authors/{id}", function($id) use ($app) {
        $author = Author::find($id);
        $author->delete();
        return $app['twig']->render('index.html.twig', array('authors' => Author::getAll()));
    });

    $app->get('/search_authors', function() use ($app) {
        $authors = Author::getAll();
        $search = strtolower($_GET['search']);
        $authors_matching_search = array();
        foreach($authors as $author) {
            if (strpos(strtolower($author->getName()), $search) !== false) {
                array_push($authors_matching_search, $author);
            }
        }
        return $app['twig']->render('search_authors.html.twig', array('authors' => $authors_matching_search));
    });

////////////////////////////////////////////////////

    $app->get('/added_patron', function() use ($app) {
        return $app['twig']->render('index.html.twig', array('patron' => Patron::getAll()));
    });


    return $app;
?>
