<?php
namespace GuzzleHttp\Cookie;
class SetCookie
{
    private static $defaults = [
        'Name'     => null,
        'Value'    => null,
        'Domain'   => null,
        'Path'     => '/',
        'Max-Age'  => null,
        'Expires'  => null,
        'Secure'   => false,
        'Discard'  => false,
        'HttpOnly' => false
    ];
    private $data;
    public static function fromString($cookie)
    {
        // Create the default return array
        $data = self::$defaults;
        // Explode the cookie string using a series of semicolons
        $pieces = array_filter(array_map('trim', explode(';', $cookie)));
        // The name of the cookie (first kvp) must exist and include an equal sign.
        if (empty($pieces[0]) || !strpos($pieces[0], '=')) {
            return new self($data);
        }

        // Add the cookie pieces into the parsed data array
        foreach ($pieces as $part) {
            $cookieParts = explode('=', $part, 2);
            $key = trim($cookieParts[0]);
            $value = isset($cookieParts[1])
                ? trim($cookieParts[1], " \n\r\t\0\x0B")
                : true;

            // Only check for non-cookies when cookies have been found
            if (empty($data['Name'])) {
                $data['Name'] = $key;
                $data['Value'] = $value;
            } else {
                foreach (array_keys(self::$defaults) as $search) {
                    if (!strcasecmp($search, $key)) {
                        $data[$search] = $value;
                        continue 2;
                    }
                }
                $data[$key] = $value;
            }
        }

        return new self($data);
    }
    public function __construct(array $data = [])
    {
        $this->data = array_replace(self::$defaults, $data);
        // Extract the Expires value and turn it into a UNIX timestamp if needed
        if (!$this->getExpires() && $this->getMaxAge()) {
            // Calculate the Expires date
            $this->setExpires(time() + $this->getMaxAge());
        } elseif ($this->getExpires() && !is_numeric($this->getExpires())) {
            $this->setExpires($this->getExpires());
        }
    }

    public function __toString()
    {
        $str = $this->data['Name'] . '=' . $this->data['Value'] . '; ';
        foreach ($this->data as $k => $v) {
            if ($k !== 'Name' && $k !== 'Value' && $v !== null && $v !== false) {
                if ($k === 'Expires') {
                    $str .= 'Expires=' . gmdate('D, d M Y H:i:s \G\M\T', $v) . '; ';
                } else {
                    $str .= ($v === true ? $k : "{$k}={$v}") . '; ';
                }
            }
        }

        return rtrim($str, '; ');
    }

    public function toArray()
    {
        return $this->data;
    }
    public function getName()
    {
        return $this->data['Name'];
    }
    public function setName($name)
    {
        $this->data['Name'] = $name;
    }
    public function getValue()
    {
        return $this->data['Value'];
    }
    public function setValue($value)
    {
        $this->data['Value'] = $value;
    }
    public function getDomain()
    {
        return $this->data['Domain'];
    }
    public function setDomain($domain)
    {
        $this->data['Domain'] = $domain;
    }
    public function getPath()
    {
        return $this->data['Path'];
    }
    public function setPath($path)
    {
        $this->data['Path'] = $path;
    }
    public function getMaxAge()
    {
        return $this->data['Max-Age'];
    }
    public function setMaxAge($maxAge)
    {
        $this->data['Max-Age'] = $maxAge;
    }
    public function getExpires()
    {
        return $this->data['Expires'];
    }
    public function setExpires($timestamp)
    {
        $this->data['Expires'] = is_numeric($timestamp)
            ? (int) $timestamp
            : strtotime($timestamp);
    }
    public function getSecure()
    {
        return $this->data['Secure'];
    }
    public function setSecure($secure)
    {
        $this->data['Secure'] = $secure;
    }
    public function getDiscard()
    {
        return $this->data['Discard'];
    }
    public function setDiscard($discard)
    {
        $this->data['Discard'] = $discard;
    }
    public function getHttpOnly()
    {
        return $this->data['HttpOnly'];
    }
    public function setHttpOnly($httpOnly)
    {
        $this->data['HttpOnly'] = $httpOnly;
    }
    public function matchesPath($requestPath)
    {
        $cookiePath = $this->getPath();

        // Match on exact matches or when path is the default empty "/"
        if ($cookiePath === '/' || $cookiePath == $requestPath) {
            return true;
        }

        // Ensure that the cookie-path is a prefix of the request path.
        if (0 !== strpos($requestPath, $cookiePath)) {
            return false;
        }

        // Match if the last character of the cookie-path is "/"
        if (substr($cookiePath, -1, 1) === '/') {
            return true;
        }

        // Match if the first character not included in cookie path is "/"
        return substr($requestPath, strlen($cookiePath), 1) === '/';
    }
    public function matchesDomain($domain)
    {
        // Remove the leading '.' as per spec in RFC 6265.
        // http://tools.ietf.org/html/rfc6265#section-5.2.3
        $cookieDomain = ltrim($this->getDomain(), '.');

        // Domain not set or exact match.
        if (!$cookieDomain || !strcasecmp($domain, $cookieDomain)) {
            return true;
        }

        // Matching the subdomain according to RFC 6265.
        // http://tools.ietf.org/html/rfc6265#section-5.1.3
        if (filter_var($domain, FILTER_VALIDATE_IP)) {
            return false;
        }

        return (bool) preg_match('/\.' . preg_quote($cookieDomain, '/') . '$/', $domain);
    }
    public function isExpired()
    {
        return $this->getExpires() !== null && time() > $this->getExpires();
    }
    public function validate()
    {
        // Names must not be empty, but can be 0
        $name = $this->getName();
        if (empty($name) && !is_numeric($name)) {
            return 'The cookie name must not be empty';
        }

        // Check if any of the invalid characters are present in the cookie name
        if (preg_match(
            '/[\x00-\x20\x22\x28-\x29\x2c\x2f\x3a-\x40\x5c\x7b\x7d\x7f]/',
            $name
        )) {
            return 'Cookie name must not contain invalid characters: ASCII '
                . 'Control characters (0-31;127), space, tab and the '
                . 'following characters: ()<>@,;:\"/?={}';
        }

        // Value must not be empty, but can be 0
        $value = $this->getValue();
        if (empty($value) && !is_numeric($value)) {
            return 'The cookie value must not be empty';
        }

        // Domains must not be empty, but can be 0
        // A "0" is not a valid internet domain, but may be used as server name
        // in a private network.
        $domain = $this->getDomain();
        if (empty($domain) && !is_numeric($domain)) {
            return 'The cookie domain must not be empty';
        }

        return true;
    }
}
