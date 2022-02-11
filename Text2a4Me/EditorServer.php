<?php
/**
 * Simple WebSocket server to handle code collaboration.
 *
 * @author Rio Astamal <rio@rioastamal.net>
 * @link https://github.com/rioastamal-examples/collaborative-editor-websocket-php
 * @license MIT
 */
namespace Text2a4Me;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class EditorServer implements MessageComponentInterface
{
    protected $tmpFile;
    protected $memoryContent = '';
    protected $prefix = 'SERVER: ';
    protected $clients = [];

    public function __construct($tmpFile)
    {
        $this->tmpFile = $tmpFile;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $data = [
            'id' => $conn->resourceId,
            'ip' => $conn->remoteAddress,
            'conn' => $conn
        ];
        $this->addClient($data);
        $this->debug("New connection -> {$conn->resourceId} ({$conn->remoteAddress})");
        $this->debug('Number of concurrent connections: ' . sizeof($this->clients));

        $content = $this->readEditorContent();
        $conn->send($content);
    }

    public function onMessage(ConnectionInterface $from, $message)
    {
        $json_message = json_decode($message, TRUE);
        if (json_last_error() === JSON_ERROR_NONE) {
            $json_message['from'] = $from->resourceId;
            $json_message['from_ip'] = $from->remoteAddress;
            $json_message['concurrent_connections'] = sizeof($this->clients);
            $message = json_encode($json_message);
        }
        $this->writeEditorContent($from, $message);
        $this->broadcastContent($from);
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->removeClient($conn->resourceId);
        $this->debug('Closed connection -> ' . $conn->resourceId);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
    }

    protected function debug($message, $newline = "\n")
    {
        printf("%s %s%s", $this->prefix, $message, $newline);
    }

    protected function addClient($clientData)
    {
        if (array_key_exists($clientData['id'], $this->clients)) {
            return;
        }

        $this->clients[$clientData['id']] = $clientData;
    }

    protected function removeClient($id)
    {
        if (array_key_exists($id, $this->clients)) {
            $this->clients = array_filter($this->clients, function($item) use ($id)
                {
                    return $item['id'] !== $id;
                }
            );
        }
    }

    protected function writeEditorContent($conn, $content)
    {
        $this->debug('Got message from ' . $conn->resourceId . ' | Message: ' . $content);
        if ($this->tmpFile === 'memory') {
            $this->memoryContent = $content;
            return;
        }

        file_put_contents($this->tmpFile, $content);
    }

    protected function readEditorContent()
    {
        if ($this->tmpFile === 'memory') {
            return $this->memoryContent;
        }

        if (! file_exists($this->tmpFile)) {
            return '';
        }

        return file_get_contents($this->tmpFile);
    }

    protected function broadcastContent(ConnectionInterface $from)
    {
        $content = $this->readEditorContent();
        $contentLog = preg_replace('#\r?\n#', '\n', $content);

        foreach ($this->clients as $id => $client) {
            if ((string)$id === (string)$from->resourceId) {
                continue;
            }

            $this->debug('Broadcast message to ' . $from->resourceId . ' -> Message: ' . $contentLog);
            $client['conn']->send($content);
        }
    }
}