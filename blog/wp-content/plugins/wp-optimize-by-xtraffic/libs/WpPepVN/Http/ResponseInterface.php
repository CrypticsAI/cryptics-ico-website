<?php 
namespace WpPepVN\Http; 

use WpPepVN\Http\Response\HeadersInterface;

/**
 * WpPepVN\Http\Response
 *
 * Interface for WpPepVN\Http\Response
 */
interface ResponseInterface
{

	/**
	 * Sets the HTTP response code
	 */
	public function setStatusCode($code, $message = null);

	/**
	 * Returns headers set by the user
	 */
	public function getHeaders();

	/**
	 * Overwrites a header in the response
	 *
	 * @param string name
	 * @param string value
	 * @return WpPepVN\Http\ResponseInterface
	 */
	public function setHeader($name, $value);

	/**
	 * Send a raw header to the response
	 */
	public function setRawHeader($header);

	/**
	 * Resets all the stablished headers
	 */
	public function resetHeaders();

	/**
	 * Sets output expire time header
	 */
	public function setExpires(\DateTime $datetime);
	/**
	 * Sends a Not-Modified response
	 */
	public function setNotModified();

	/**
	 * Sets the response content-type mime, optionally the charset
	 *
	 * @param string contentType
	 * @param string charset
	 * @return WpPepVN\Http\ResponseInterface
	 */
	public function setContentType($contentType, $charset = null);

	/**
	 * Redirect by HTTP to another action or URL
	 *
	 * @param string location
	 * @param boolean externalRedirect
	 * @param int statusCode
	 * @return WpPepVN\Http\ResponseInterface
	 */
	public function redirect($location = null, $externalRedirect = false, $statusCode = 302);

	/**
	 * Sets HTTP response body
	 */
	public function setContent($content);

	/**
	 * Sets HTTP response body. The parameter is automatically converted to JSON
	 *
	 *<code>
	 *	response->setJsonContent(array("status" => "OK"));
	 *</code>
	 *
	 * @param string content
	 * @return WpPepVN\Http\ResponseInterface
	 */
	public function setJsonContent($content);

	/**
	 * Appends a string to the HTTP response body
	 *
	 * @param string content
	 * @return WpPepVN\Http\ResponseInterface
	 */
	public function appendContent($content);

	/**
	 * Gets the HTTP response body
	 */
	public function getContent();

	/**
	 * Sends headers to the client
	 */
	public function sendHeaders();

	/**
	 * Sends cookies to the client
	 */
	public function sendCookies();

	/**
	 * Prints out HTTP response to the client
	 */
	public function send();

	/**
	 * Sets an attached file to be sent at the end of the request
	 *
	 * @param string filePath
	 * @param string attachmentName
	 */
	public function setFileToSend($filePath, $attachmentName = null);

}