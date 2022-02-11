# Text.2a4.me

This project was cloned from [Collaborative Editor Websocket PHP](https://github.com/rioastamal-examples/collaborative-editor-websocket-php) and turned into this robust piece of software.

Requires:
* Composer
* Vue

# Installation

Clone this project repository.

```bash
$ git clone https://github.com/jvjvjv/text-2a4-me
```

Install all dependencies.

```bash
$ cd text-2a4-me
$ composer install
$ npm install
```

# Configuration

Create your `.env` file.

```dotenv
CHAT_BIND_ADDR=0.0.0.0  # Websocket bind address
CHAT_SERVER_PORT=8080   # Websocket bind port
HOST=localhost          # Vue Serve bind address
PORT=5000               # Vue Serve bind port
VUE_APP_WEBSOCKET_HOST=$CHAT_BIND_ADDR
VUE_APP_WEBSOCKET_PORT=$CHAT_SERVER_PORT
```



# Building the app

The app uses the Vue CLI to build

```bash
$ npm run build
```

# Development

The app uses the Vue CLI to serve

```npm
$ npm run serve

> text.2a4.me@2.0.0 serve /Users/jasonv/Projects/text.2a4.me
> vue-cli-service serve

  App running at:
  - Local:   http://localhost:5000/ 
  - Network: http://192.168.16.37:5000/

  Note that the development build is not optimized.
  To create a production build, run npm run build.
```

# Running the app

The first part of the application is the web socket server.

```bash
$ npm run websocket

> text.2a4.me@2.0.0 websocket /root/Projects/text.2a4.me
> php ./bin/editor-server.php
Websocket Editor server running on 0.0.0.0:3479.
```

The second part of the application is the VueJS editor.

Now open another terminal and start web server using PHP built-in web server.
The document root should be `public/` directory.

```bash
$ php -S 0.0.0.0:8081 -t public/
PHP 7.1.25 Development Server started at Wed Jan  9 06:10:13 2019
Listening on http://0.0.0.0:8081
Document root is /path/to/collaborative-editor-websocket-php/public
Press Ctrl-C to quit.
```