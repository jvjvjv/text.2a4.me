# Text.2a4.me

This project was cloned from [Collaborative Editor Websocket PHP](https://github.com/rioastamal-examples/collaborative-editor-websocket-php) and turned into this robust piece of software.

# Installation

Clone this project repository.

```
$ git clone https://github.com/jvjvjv/text-2a4-me
```

Install all dependencies.

```
$ cd text-2a4-me
$ composer install -vvv
$ npm install
```

# Building the app

The app uses the Vue CLI to build

```
npm run build
```

# Running the app

The first part of the application is the web socket server.

```
$ php ./bin/editor-server.php
Websocket Editor server running on 0.0.0.0:8080.
```

The second part of the application is the VueJS editor.

Now open another terminal and start web server using PHP built-in web server.
The document root should be `public/` directory.

```
$ php -S 0.0.0.0:8081 -t public/
PHP 7.1.25 Development Server started at Wed Jan  9 06:10:13 2019
Listening on http://0.0.0.0:8081
Document root is /path/to/collaborative-editor-websocket-php/public
Press Ctrl-C to quit.
```