<?php

if ( !class_exists('Puc_v4p11_Vcs_GitLabApi', false) ):

	class Puc_v4p11_Vcs_GitLabApi extends Puc_v4p11_Vcs_Api {
		protected $userName;
		protected $repositoryHost;
		protected $repositoryProtocol = 'https';
		protected $repositoryName;
		protected $accessToken;

		public function __construct($repositoryUrl, $accessToken = null, $subgroup = null) {
			//Parse the repository host to support custom hosts.
			$port = parse_url($repositoryUrl, PHP_URL_PORT);
			if ( !empty($port) ) {
				$port = ':' . $port;
			}
			$this->repositoryHost = parse_url($repositoryUrl, PHP_URL_HOST) . $port;

			if ( $this->repositoryHost !== 'gitlab.com' ) {
				$this->repositoryProtocol = parse_url($repositoryUrl, PHP_URL_SCHEME);
			}

			//Find the repository information
			$path = parse_url($repositoryUrl, PHP_URL_PATH);
			if ( preg_match('@^/?(?P<username>[^/]+?)/(?P<repository>[^/#?&]+?)/?$@', $path, $matches) ) {
				$this->userName = $matches['username'];
				$this->repositoryName = $matches['repository'];
			} elseif ( ($this->repositoryHost === 'gitlab.com') ) {
				//This is probably a repository in a subgroup, e.g. "/organization/category/repo".
				$parts = explode('/', trim($path, '/'));
				if ( count($parts) < 3 ) {
					throw new InvalidArgumentException('Invalid GitLab.com repository URL: "' . $repositoryUrl . '"');
				}
				$lastPart = array_pop($parts);
				$this->userName = implode('/', $parts);
				$this->repositoryName = $lastPart;
			} else {
				//There could be subgroups in the URL:  gitlab.domain.com/group/subgroup/subgroup2/repository
				if ( $subgroup !== null ) {
					$path = str_replace(trailingslashit($subgroup), '', $path);
				}

				//This is not a traditional url, it could be gitlab is in a deeper subdirectory.
				//Get the path segments.
				$segments = explode('/', untrailingslashit(ltrim($path, '/')));

				//We need at least /user-name/repository-name/
				if ( count($segments) < 2 ) {
					throw new InvalidArgumentException('Invalid GitLab repository URL: "' . $repositoryUrl . '"');
				}

				//Get the username and repository name.
				$usernameRepo = array_splice($segments, -2, 2);
				$this->userName = $usernameRepo[0];
				$this->repositoryName = $usernameRepo[1];

				//Append the remaining segments to the host if there are segments left.
				if ( count($segments) > 0 ) {
					$this->repositoryHost = trailingslashit($this->repositoryHost) . implode('/', $segments);
				}

				//Add subgroups to username.
				if ( $subgroup !== null ) {
					$this->userName = $usernameRepo[0] . '/' . untrailingslashit($subgroup);
				}
			}

			parent::__construct($repositoryUrl, $accessToken);
		}
		public function getLatestRelease() {
			return $this->getLatestTag();
		}
		public function getLatestTag() {
			$tags = $this->api('/:id/repository/tags');
			if ( is_wp_error($tags) || empty($tags) || !is_array($tags) ) {
				return null;
			}

			$versionTags = $this->sortTagsByVersion($tags);
			if ( empty($versionTags) ) {
				return null;
			}

			$tag = $versionTags[0];
			return new Puc_v4p11_Vcs_Reference(array(
				'name'        => $tag->name,
				'version'     => ltrim($tag->name, 'v'),
				'downloadUrl' => $this->buildArchiveDownloadUrl($tag->name),
				'apiResponse' => $tag,
			));
		}
		public function getBranch($branchName) {
			$branch = $this->api('/:id/repository/branches/' . $branchName);
			if ( is_wp_error($branch) || empty($branch) ) {
				return null;
			}

			$reference = new Puc_v4p11_Vcs_Reference(array(
				'name'        => $branch->name,
				'downloadUrl' => $this->buildArchiveDownloadUrl($branch->name),
				'apiResponse' => $branch,
			));

			if ( isset($branch->commit, $branch->commit->committed_date) ) {
				$reference->updated = $branch->commit->committed_date;
			}

			return $reference;
		}
		public function getLatestCommitTime($ref) {
			$commits = $this->api('/:id/repository/commits/', array('ref_name' => $ref));
			if ( is_wp_error($commits) || !is_array($commits) || !isset($commits[0]) ) {
				return null;
			}

			return $commits[0]->committed_date;
		}
		protected function api($url, $queryParams = array()) {
			$baseUrl = $url;
			$url = $this->buildApiUrl($url, $queryParams);

			$options = array('timeout' => 10);
			if ( !empty($this->httpFilterName) ) {
				$options = apply_filters($this->httpFilterName, $options);
			}

			$response = wp_remote_get($url, $options);
			if ( is_wp_error($response) ) {
				do_action('puc_api_error', $response, null, $url, $this->slug);
				return $response;
			}

			$code = wp_remote_retrieve_response_code($response);
			$body = wp_remote_retrieve_body($response);
			if ( $code === 200 ) {
				return json_decode($body);
			}

			$error = new WP_Error(
				'puc-gitlab-http-error',
				sprintf('GitLab API error. URL: "%s",  HTTP status code: %d.', $baseUrl, $code)
			);
			do_action('puc_api_error', $error, $response, $url, $this->slug);

			return $error;
		}
		protected function buildApiUrl($url, $queryParams) {
			$variables = array(
				'user' => $this->userName,
				'repo' => $this->repositoryName,
				'id'   => $this->userName . '/' . $this->repositoryName,
			);

			foreach ($variables as $name => $value) {
				$url = str_replace("/:{$name}", '/' . urlencode($value), $url);
			}

			$url = substr($url, 1);
			$url = sprintf('%1$s://%2$s/api/v4/projects/%3$s', $this->repositoryProtocol, $this->repositoryHost, $url);

			if ( !empty($this->accessToken) ) {
				$queryParams['private_token'] = $this->accessToken;
			}

			if ( !empty($queryParams) ) {
				$url = add_query_arg($queryParams, $url);
			}

			return $url;
		}
		public function getRemoteFile($path, $ref = 'master') {
			$response = $this->api('/:id/repository/files/' . $path, array('ref' => $ref));
			if ( is_wp_error($response) || !isset($response->content) || $response->encoding !== 'base64' ) {
				return null;
			}

			return base64_decode($response->content);
		}
		public function buildArchiveDownloadUrl($ref = 'master') {
			$url = sprintf(
				'%1$s://%2$s/api/v4/projects/%3$s/repository/archive.zip',
				$this->repositoryProtocol,
				$this->repositoryHost,
				urlencode($this->userName . '/' . $this->repositoryName)
			);
			$url = add_query_arg('sha', urlencode($ref), $url);

			if ( !empty($this->accessToken) ) {
				$url = add_query_arg('private_token', $this->accessToken, $url);
			}

			return $url;
		}
		public function getTag($tagName) {
			throw new LogicException('The ' . __METHOD__ . ' method is not implemented and should not be used.');
		}
		public function chooseReference($configBranch) {
			$updateSource = null;

			// GitLab doesn't handle releases the same as GitHub so just use the latest tag
			if ( $configBranch === 'master' ) {
				$updateSource = $this->getLatestTag();
			}

			if ( empty($updateSource) ) {
				$updateSource = $this->getBranch($configBranch);
			}

			return $updateSource;
		}

		public function setAuthentication($credentials) {
			parent::setAuthentication($credentials);
			$this->accessToken = is_string($credentials) ? $credentials : null;
		}
	}

endif;
