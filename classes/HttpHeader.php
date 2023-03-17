<?php

declare(strict_types=1);



class HttpHeader
{

	private static function decodeHttpResponse(HttpStatusCode $statusCode): string
	{
		return match ($statusCode) {
			HttpStatusCode::Continue => 'Continue',
			HttpStatusCode::SwitchingProtocols => 'Switching Protocols',
			HttpStatusCode::OK => 'OK',
			HttpStatusCode::Created => 'Created',
			HttpStatusCode::Accepted => 'Accepted',
			HttpStatusCode::NonAuthoritativeInformation => 'Non-Authoritative Information',
			HttpStatusCode::NoContent => 'No Content',
			HttpStatusCode::ResetContent => 'Reset Content',
			HttpStatusCode::PartialContent => 'Partial Content',
			HttpStatusCode::MultipleChoices => 'Multiple Choices',
			HttpStatusCode::MovedPermanently => 'Moved Permanently',
			HttpStatusCode::Found => 'Found',
			HttpStatusCode::SeeOther => 'See Other',
			HttpStatusCode::NotModified => 'Not Modified',
			HttpStatusCode::UseProxy => 'Use Proxy',
			HttpStatusCode::TemporaryRedirect => 'Temporary Redirect',
			HttpStatusCode::BadRequest => 'Bad Request',
			HttpStatusCode::Unauthorized => 'Unauthorized',
			HttpStatusCode::PaymentRequired => 'Payment Required',
			HttpStatusCode::Forbidden => 'Forbidden',
			HttpStatusCode::NotFound => 'Not Found',
			HttpStatusCode::MethodNotAllowed => 'Method Not Allowed',
			HttpStatusCode::NotAcceptable => 'Not Acceptable',
			HttpStatusCode::ProxyAuthenticationRequired => 'Proxy Authentication Required',
			HttpStatusCode::RequestTimeout => 'Request Timeout',
			HttpStatusCode::Conflict => 'Conflict',
			HttpStatusCode::Gone => 'Gone',
			HttpStatusCode::LengthRequired => 'Length Required',
			HttpStatusCode::PreconditionFailed => 'Precondition Failed',
			HttpStatusCode::RequestEntityTooLarge => 'Request Entity Too Large',
			HttpStatusCode::RequestURITooLong => 'Request-URI Too Long',
			HttpStatusCode::UnsupportedMediaType => 'Unsupported Media Type',
			HttpStatusCode::RequestedRangeNotSatisfiable => 'Requested Range Not Satisfiable',
			HttpStatusCode::ExpectationFailed => 'Expectation Failed',
			HttpStatusCode::InternalServerError => 'Internal Server Error',
			HttpStatusCode::NotImplemented => 'Not Implemented',
			HttpStatusCode::BadGateway => 'Bad Gateway',
			HttpStatusCode::ServiceUnavailable => 'Service Unavailable',
			HttpStatusCode::GatewayTimeout => 'Gateway Timeout',
			HttpStatusCode::HTTPVersionNotSupported => 'HTTP Version Not Supported',
			default => 'Unknown HTTP status code'
		};
	}
	public static function sendAccessControlAllowOrigin(?string $origin = null): void
	{
		header("Access-Control-Allow-Origin: " . ($origin ?? '*'));
	}
	public static function sendAccessControlAllowMethods(?string $methods = null): void
	{
		header("Access-Control-Allow-Methods: " . ($methods ?? 'OPTIONS,GET,POST,PUT,DELETE'));
	}
	public static function sendAccessControlMaxAge(?int $maxAge = 3600): void
	{
		header("Access-Control-Max-Age: " . ($maxAge ?? 3600));
	}
	public static function sendAccessControlAllowHeaders(?string $headers = null): void
	{
		header("Access-Control-Allow-Headers: " . ($headers ?? 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'));
	}
	public static function sendStatusCode(HttpStatusCode $httpStatusCode): void
	{
		header(
			sprintf(
				'HTTP/1.1 %d %s',
				$httpStatusCode->value,
				self::decodeHttpResponse($httpStatusCode)
			)
		);
		if ($httpStatusCode-> value >= 400 && $httpStatusCode-> value < 600) {
			header("Content-Type: application/problem+json; charset=UTF-8");
		} else {
			header("Content-Type: application/json; charset=UTF-8");
		}
	}

	/**
	 * set the headers for the response
	 */
	public function sendResponseHeader(HttpStatusCode $httpStatusCode): void
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
		header("Access-Control-Max-Age: 3600");
		header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

		header(
			sprintf(
				'HTTP/1.1 %d %s',
				(int)$httpStatusCode,
				self::decodeHttpResponse($httpStatusCode)
			)
		);
		header("Content-Type: application/json; charset=UTF-8");
		$response['body'] = null;
	}
};
