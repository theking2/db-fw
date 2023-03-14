<?php

declare(strict_types=1);

enum HttpStatusCode: int
{
	case Continue = 100;
	case  SwitchingProtocols = 101;
	case  OK = 200;
	case  Created = 201;
	case  Accepted = 202;
	case  NonAuthoritativeInformation = 203;
	case  NoContent = 204;
	case  ResetContent = 205;
	case  PartialContent = 206;
	case  MultipleChoices = 300;
	case  MovedPermanently = 301;
	case  Found = 302;
	case  SeeOther = 303;
	case  NotModified = 304;
	case  UseProxy = 305;
	case  TemporaryRedirect = 307;
	case  BadRequest = 400;
	case  Unauthorized = 401;
	case  PaymentRequired = 402;
	case  Forbidden = 403;
	case  NotFound = 404;
	case  MethodNotAllowed = 405;
	case  NotAcceptable = 406;
	case  ProxyAuthenticationRequired = 407;
	case  RequestTimeout = 408;
	case  Conflict = 409;
	case  Gone = 410;
	case  LengthRequired = 411;
	case  PreconditionFailed = 412;
	case  RequestEntityTooLarge = 413;
	case  RequestURITooLong = 414;
	case  UnsupportedMediaType = 415;
	case  RequestedRangeNotSatisfiable = 416;
	case  ExpectationFailed = 417;
	case  InternalServerError = 500;
	case  NotImplemented = 501;
	case  BadGateway = 502;
	case  ServiceUnavailable = 503;
	case  GatewayTimeout = 504;
	case  HTTPVersionNotSupported = 505;
}

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
	public static function sendAccessControlMaxAge(?int $maxAge = null): void
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
				(int)$httpStatusCode, 
				self::decodeHttpResponse($httpStatusCode)
			) 
		);
		if((int)$httpStatusCode >= 400 && (int)$httpStatusCode < 600) {
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
