# PHPRouting

Simple PHP script for routing on Apache Webserver. 

### Usage

#### Basic Example

**routing/Loader.php:**

```php

public static function handle() : void
{

    Router::init();
    
    # Insert your routes between these init() and run() functions
    
    # GET Request
    Router::get("/", function(Request $req, Response $res) {
    
        # $req is Request object with access to headers, files, body etc.
        # $res is Response object to send data back to client
        
        $res->send("Hello!");
    
    }
    
    Router::run();

}

```

#### Send File

**routing/Loader.php:**

```php

public static function handle() : void
{

    Router::init();
    
    Router::get("/filexy", function(Request $req, Response $res) {
        
        $res->send_file(__DIR__ . "/filexy.html");
    
    }
    
    Router::run();

}

```

#### Handle POST request

**routing/Loader.php:**

```php

public static function handle() : void
{

    Router::init();
    
    Router::post("/api", function(Request $req, Response $res) {
        
        $json_obj = $req->get_body();
        
        $result = // ... work with post data
       
        if($result["type"]) {
            $res->send_sucess($result["data"]);
        } else {
            $res->send_error("Failure message");
        }
    
    }
    
    Router::run();

}

```