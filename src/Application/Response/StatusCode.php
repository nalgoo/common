<?php
declare(strict_types=1);

namespace Nalgoo\Common\Application\Response;

class StatusCode
{
	/**
	 * Standard response for successful HTTP requests. The actual response will depend on the request method used.
	 * In a GET request, the response will contain an entity corresponding to the requested resource. In a POST request
	 * the response will contain an entity describing or containing the result of the action.
	 */
	const SUCCESS_OK = 200;

	/**
	 * The request has been fulfilled and resulted in a new resource being created.
	 *
	 * Successful creation occurred (via either POST or PUT). Set the Location header to contain a link
	 * to the newly-created resource (on POST). Response body content may or may not be present.
	 */
	const SUCCESS_CREATED = 201;

	/**
	 * The request has been accepted for processing, but the processing has not been completed. The request might
	 * or might not eventually be acted upon, as it might be disallowed when processing actually takes place.
	 */
	const SUCCESS_ACCEPTED = 202;

	/**
	 * The server successfully processed the request, but is not returning any content.
	 */
	const SUCCESS_NO_CONTENT = 204;

	/**
	 * This and all future requests should be directed to the given URI
	 *
	 * Note: When automatically redirecting a POST request after receiving a 301 status code, some existing HTTP/1.0
	 * user agents will erroneously change it into a GET request.
	 */
	const REDIRECTION_MOVED_PERMANENTLY = 301;

	/**
	 * The response to the request can be found under another URI using a GET method. When received in response
	 * to a POST (or PUT/DELETE), it should be assumed that the server has received the data and the redirect
	 * should be issued with a separate GET message.
	 */
	const REDIRECTION_SEE_OTHER = 303;

	/**
	 * The request should be repeated with another URI; however, future requests can still use the original URI.
	 * In contrast to 302, the request method should not be changed when reissuing the original request. For instance,
	 * a POST request must be repeated using another POST request.
	 */
	const REDIRECTION_TEMPORARY_REDIRECT = 307;

	/**
	 * The request and all future requests should be repeated using another URI
	 * (experimental)
	 */
	const REDIRECTION_PERMANENT_REDIRECT = 308;

	const ERROR_BAD_REQUEST        = 400;
	const ERROR_UNAUTHORIZED       = 401;
	const ERROR_FORBIDDEN          = 403;
	const ERROR_NOT_FOUND          = 404;
	const ERROR_METHOD_NOT_ALLOWED = 405;
	const ERROR_NOT_ACCEPTABLE     = 406;
	const ERROR_REQUEST_TIMEOUT    = 408;
	const ERROR_CONFLICT           = 409;
	const ERROR_REQUEST_ENTITY_TOO_LARGE = 413;
	const ERROR_UNSUPPORTED_MEDIA_TYPE   = 415;
	const ERROR_UNPROCESSABLE_ENTITY     = 422;
	const ERROR_TOO_MANY_REQUESTS        = 429;

	const INTERNAL_SERVER_ERROR = 500;
	const NOT_IMPLEMENTED = 501;
	const BAD_GATEWAY = 502;

}

