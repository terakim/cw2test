<?php

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

final class Utils
{
    public static function caselessRemove($keys, array $data)
    {
        $result = [];

        foreach ($keys as &$key) {
            $key = strtolower($key);
        }

        foreach ($data as $k => $v) {
            if (!in_array(strtolower($k), $keys)) {
                $result[$k] = $v;
            }
        }

        return $result;
    }
    public static function copyToStream(StreamInterface $source, StreamInterface $dest, $maxLen = -1)
    {
        $bufferSize = 8192;

        if ($maxLen === -1) {
            while (!$source->eof()) {
                if (!$dest->write($source->read($bufferSize))) {
                    break;
                }
            }
        } else {
            $remaining = $maxLen;
            while ($remaining > 0 && !$source->eof()) {
                $buf = $source->read(min($bufferSize, $remaining));
                $len = strlen($buf);
                if (!$len) {
                    break;
                }
                $remaining -= $len;
                $dest->write($buf);
            }
        }
    }
    public static function copyToString(StreamInterface $stream, $maxLen = -1)
    {
        $buffer = '';

        if ($maxLen === -1) {
            while (!$stream->eof()) {
                $buf = $stream->read(1048576);
                // Using a loose equality here to match on '' and false.
                if ($buf == null) {
                    break;
                }
                $buffer .= $buf;
            }
            return $buffer;
        }

        $len = 0;
        while (!$stream->eof() && $len < $maxLen) {
            $buf = $stream->read($maxLen - $len);
            // Using a loose equality here to match on '' and false.
            if ($buf == null) {
                break;
            }
            $buffer .= $buf;
            $len = strlen($buffer);
        }

        return $buffer;
    }
    public static function hash(StreamInterface $stream, $algo, $rawOutput = false)
    {
        $pos = $stream->tell();

        if ($pos > 0) {
            $stream->rewind();
        }

        $ctx = hash_init($algo);
        while (!$stream->eof()) {
            hash_update($ctx, $stream->read(1048576));
        }

        $out = hash_final($ctx, (bool) $rawOutput);
        $stream->seek($pos);

        return $out;
    }
    public static function modifyRequest(RequestInterface $request, array $changes)
    {
        if (!$changes) {
            return $request;
        }

        $headers = $request->getHeaders();

        if (!isset($changes['uri'])) {
            $uri = $request->getUri();
        } else {
            // Remove the host header if one is on the URI
            if ($host = $changes['uri']->getHost()) {
                $changes['set_headers']['Host'] = $host;

                if ($port = $changes['uri']->getPort()) {
                    $standardPorts = ['http' => 80, 'https' => 443];
                    $scheme = $changes['uri']->getScheme();
                    if (isset($standardPorts[$scheme]) && $port != $standardPorts[$scheme]) {
                        $changes['set_headers']['Host'] .= ':' . $port;
                    }
                }
            }
            $uri = $changes['uri'];
        }

        if (!empty($changes['remove_headers'])) {
            $headers = self::caselessRemove($changes['remove_headers'], $headers);
        }

        if (!empty($changes['set_headers'])) {
            $headers = self::caselessRemove(array_keys($changes['set_headers']), $headers);
            $headers = $changes['set_headers'] + $headers;
        }

        if (isset($changes['query'])) {
            $uri = $uri->withQuery($changes['query']);
        }

        if ($request instanceof ServerRequestInterface) {
            $new = (new ServerRequest(
                isset($changes['method']) ? $changes['method'] : $request->getMethod(),
                $uri,
                $headers,
                isset($changes['body']) ? $changes['body'] : $request->getBody(),
                isset($changes['version'])
                    ? $changes['version']
                    : $request->getProtocolVersion(),
                $request->getServerParams()
            ))
            ->withParsedBody($request->getParsedBody())
            ->withQueryParams($request->getQueryParams())
            ->withCookieParams($request->getCookieParams())
            ->withUploadedFiles($request->getUploadedFiles());

            foreach ($request->getAttributes() as $key => $value) {
                $new = $new->withAttribute($key, $value);
            }

            return $new;
        }

        return new Request(
            isset($changes['method']) ? $changes['method'] : $request->getMethod(),
            $uri,
            $headers,
            isset($changes['body']) ? $changes['body'] : $request->getBody(),
            isset($changes['version'])
                ? $changes['version']
                : $request->getProtocolVersion()
        );
    }
    public static function readLine(StreamInterface $stream, $maxLength = null)
    {
        $buffer = '';
        $size = 0;

        while (!$stream->eof()) {
            // Using a loose equality here to match on '' and false.
            if (null == ($byte = $stream->read(1))) {
                return $buffer;
            }
            $buffer .= $byte;
            // Break when a new line is found or the max length - 1 is reached
            if ($byte === "\n" || ++$size === $maxLength - 1) {
                break;
            }
        }

        return $buffer;
    }
    public static function streamFor($resource = '', array $options = [])
    {
        if (is_scalar($resource)) {
            $stream = self::tryFopen('php://temp', 'r+');
            if ($resource !== '') {
                fwrite($stream, $resource);
                fseek($stream, 0);
            }
            return new Stream($stream, $options);
        }

        switch (gettype($resource)) {
            case 'resource':
                $metaData = \stream_get_meta_data($resource);
                if (isset($metaData['uri']) && $metaData['uri'] === 'php://input') {
                    $stream = self::tryFopen('php://temp', 'w+');
                    fwrite($stream, stream_get_contents($resource));
                    fseek($stream, 0);
                    $resource = $stream;
                }
                return new Stream($resource, $options);
            case 'object':
                if ($resource instanceof StreamInterface) {
                    return $resource;
                } elseif ($resource instanceof \Iterator) {
                    return new PumpStream(function () use ($resource) {
                        if (!$resource->valid()) {
                            return false;
                        }
                        $result = $resource->current();
                        $resource->next();
                        return $result;
                    }, $options);
                } elseif (method_exists($resource, '__toString')) {
                    return Utils::streamFor((string) $resource, $options);
                }
                break;
            case 'NULL':
                return new Stream(self::tryFopen('php://temp', 'r+'), $options);
        }

        if (is_callable($resource)) {
            return new PumpStream($resource, $options);
        }

        throw new \InvalidArgumentException('Invalid resource type: ' . gettype($resource));
    }
    public static function tryFopen($filename, $mode)
    {
        $ex = null;
        set_error_handler(function () use ($filename, $mode, &$ex) {
            $ex = new \RuntimeException(sprintf(
                'Unable to open "%s" using mode "%s": %s',
                $filename,
                $mode,
                func_get_args()[1]
            ));

            return true;
        });

        try {
            $handle = fopen($filename, $mode);
        } catch (\Throwable $e) {
            $ex = new \RuntimeException(sprintf(
                'Unable to open "%s" using mode "%s": %s',
                $filename,
                $mode,
                $e->getMessage()
            ), 0, $e);
        }

        restore_error_handler();

        if ($ex) {
            throw $ex;
        }

        return $handle;
    }
    public static function uriFor($uri)
    {
        if ($uri instanceof UriInterface) {
            return $uri;
        }

        if (is_string($uri)) {
            return new Uri($uri);
        }

        throw new \InvalidArgumentException('URI must be a string or UriInterface');
    }
}
