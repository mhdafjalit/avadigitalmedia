<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
#[AllowDynamicProperties]
class Dmailer
{
	
	private $CI;
	private $smtp_active = true;
	private $email_server = 'gmail'; // Default: 'gmail', 'auto', 'hostinger', 'milesweb', 'cpanel'
	private $is_live_server = false;
		
    public function __construct()
	{
		
		$this->CI =& get_instance();	
		$this->CI->load->library('email');	
		
		// Detect if running on live server (not localhost)
		$host = $_SERVER['HTTP_HOST'] ?? '';
		$localhost_patterns = ['localhost', '127.0.0.1', '::1', '.test', '.local'];
		$this->is_live_server = true;
		foreach($localhost_patterns as $pattern) {
			if(strpos($host, $pattern) !== false) {
				$this->is_live_server = false;
				break;
			}
		}
		
		// Auto-select email server based on environment
		if($this->is_live_server && $this->email_server == 'gmail') {
			// On live server, automatically use hosting provider's mail
			$this->email_server = 'auto';
		}
		
		// Override via URL parameters (for testing)
		if(isset($_GET['bvm'])){
			$this->smtp_active = ($_GET['bvm'] == 1);
		}
		if(isset($_GET['server'])){
			$this->email_server = $_GET['server'];
		}
		
		$this->x_use_smtp = $this->smtp_active ? 1 : 0;
		
		// Configure email based on settings
		if($this->smtp_active){
			$this->configure_smtp_by_server();
		} else {
			$this->configure_local_mail();
		}
	}
	
	/**
	 * Configure SMTP based on selected email server
	 */
	private function configure_smtp_by_server()
	{
		$config = array();
		
		switch($this->email_server) {
			case 'gmail':
				$config = array(
					'protocol' => 'smtp',
					'smtp_host' => 'smtp.gmail.com',
					'smtp_port' => 587,
					'smtp_user' => 'mhdafjalit786@gmail.com',
					'smtp_pass' => 'rcdh udzi oazr ljdd',
					'smtp_crypto' => 'tls',
					'mailtype' => 'html',
					'charset' => 'utf-8',
					'newline' => "\r\n",
					'crlf' => "\r\n",
					'wordwrap' => TRUE
				);
				break;
			
			case 'hostinger':
				$config = array(
					'protocol' => 'smtp',
					'smtp_host' => 'smtp.hostinger.com',
					'smtp_port' => 465,
					'smtp_user' => $this->get_email_user(),
					'smtp_pass' => $this->get_email_password(),
					'smtp_crypto' => 'ssl',
					'mailtype' => 'html',
					'charset' => 'utf-8',
					'newline' => "\r\n",
					'crlf' => "\r\n",
					'wordwrap' => TRUE
				);
				break;
			
			case 'milesweb':
				$config = array(
					'protocol' => 'smtp',
					'smtp_host' => 'mail.yourdomain.com', // Update with Milesweb SMTP
					'smtp_port' => 465,
					'smtp_user' => $this->get_email_user(),
					'smtp_pass' => $this->get_email_password(),
					'smtp_crypto' => 'ssl',
					'mailtype' => 'html',
					'charset' => 'utf-8',
					'newline' => "\r\n",
					'crlf' => "\r\n",
					'wordwrap' => TRUE
				);
				break;
			
			case 'cpanel':
				$config = array(
					'protocol' => 'smtp',
					'smtp_host' => 'mail.' . $this->get_domain(),
					'smtp_port' => 465,
					'smtp_user' => $this->get_email_user(),
					'smtp_pass' => $this->get_email_password(),
					'smtp_crypto' => 'ssl',
					'mailtype' => 'html',
					'charset' => 'utf-8',
					'newline' => "\r\n",
					'crlf' => "\r\n",
					'wordwrap' => TRUE
				);
				break;
			
			case 'auto':
				// Auto-detect server configuration
				$config = $this->auto_configure_server();
				break;
				
			case 'custom':
			default:
				$config = array(
					'protocol' => 'smtp',
					'smtp_host' => $this->CI->config->item('smtp_host') ?: 'smtp.yourdomain.com',
					'smtp_port' => $this->CI->config->item('smtp_port') ?: 587,
					'smtp_user' => $this->CI->config->item('smtp_user') ?: $this->get_email_user(),
					'smtp_pass' => $this->CI->config->item('smtp_pass') ?: $this->get_email_password(),
					'smtp_crypto' => $this->CI->config->item('smtp_crypto') ?: 'tls',
					'mailtype' => 'html',
					'charset' => 'utf-8',
					'newline' => "\r\n",
					'crlf' => "\r\n",
					'wordwrap' => TRUE
				);
				break;
		}
		
		$this->CI->email->initialize($config);
	}
	
	/**
	 * Auto-configure based on server environment
	 */
	private function auto_configure_server()
	{
		// Try to detect hosting provider
		$host = $_SERVER['HTTP_HOST'] ?? '';
		
		// Default configuration for most cPanel hosts
		$config = array(
			'protocol' => 'smtp',
			'smtp_host' => 'mail.' . $host,
			'smtp_port' => 465,
			'smtp_user' => $this->get_email_user(),
			'smtp_pass' => $this->get_email_password(),
			'smtp_crypto' => 'ssl',
			'mailtype' => 'html',
			'charset' => 'utf-8',
			'newline' => "\r\n",
			'crlf' => "\r\n",
			'wordwrap' => TRUE
		);
		
		// If mail.domain.com doesn't work, try localhost
		$config['smtp_host'] = 'localhost';
		$config['smtp_port'] = 25;
		$config['smtp_crypto'] = '';
		
		return $config;
	}
	
	/**
	 * Get email user from config or environment
	 */
	private function get_email_user()
	{
		// You can store this in database or config file
		$email_user = $this->CI->config->item('smtp_user');
		if(!$email_user) {
			// Get from .env or default
			$email_user = 'noreply@' . $this->get_domain();
		}
		return $email_user;
	}
	
	/**
	 * Get email password from config or environment
	 */
	private function get_email_password()
	{
		return $this->CI->config->item('smtp_pass') ?: '';
	}
	
	/**
	 * Get current domain
	 */
	private function get_domain()
	{
		$host = $_SERVER['HTTP_HOST'] ?? '';
		// Remove port if present
		$host = preg_replace('/:\d+$/', '', $host);
		// Remove www if present
		$host = preg_replace('/^www\./', '', $host);
		return $host;
	}
	
	/**
	 * Configure Local Mail Server (Development)
	 */
	private function configure_local_mail()
	{
		$config = array(
			'protocol' => 'mail',
			'mailtype' => 'html',
			'charset' => 'utf-8',
			'newline' => "\r\n",
			'crlf' => "\r\n",
			'wordwrap' => TRUE
		);
		
		$this->CI->email->initialize($config);
	}

	public function mail_notify($mail_conf = array()){
		if($this->smtp_active || (isset($mail_conf['libtype']) && $mail_conf['libtype']=='sendgrid')){
			if(file_exists(APPPATH . 'vendor/autoload.php')){
				require_once APPPATH . 'vendor/autoload.php';
			}
			$this->mail_notify_sendgrid($mail_conf);
		}else{
			$this->mail_notify_ci($mail_conf);
		}
	}
   
	public function mail_notify_ci($mail_conf = array())
	{
		if(is_array($mail_conf) && !empty($mail_conf))
		{	   	 
			$mail_to            = $mail_conf['to_email'];
			$mail_subject       = $mail_conf['subject']; 
			$from_email         = $mail_conf['from_email'];
			$from_name          = $mail_conf['from_name'];	
			$body               = $mail_conf['body_part'];				
			$file               = @$mail_conf['attachment'];
			$cc                 = @$mail_conf['cc'];
			$bcc                = @$mail_conf['bcc'];
			$alternative_msg    = @$mail_conf['alternative_msg'];
			$debug              = @$mail_conf['debug'];

			if($mail_to != '')
			{		
				$this->CI->email->clear(TRUE);
				$this->CI->email->set_newline("\r\n");
				$this->CI->email->set_mailtype('html');				  
				$this->CI->email->from($from_email, $from_name);
				$this->CI->email->reply_to($from_email, $from_name);
				$this->CI->email->to($mail_to);
				
				if($cc != '')
				{
					$this->CI->email->cc($cc);
				}
				
				if($bcc != '')
				{
					$this->CI->email->bcc($bcc);
				}
				
				if($alternative_msg != '')
				{					
					$this->CI->email->set_alt_message($alternative_msg);					
				}
				
				if(is_array($file) && count($file) > 0)
				{
					foreach($file as $attach_path)
					{
						if($attach_path != '' && file_exists($attach_path))
						{
							$this->CI->email->attach($attach_path);
						}
					}
				}
				else
				{
					if($file != '' && file_exists($file))
					{
						$this->CI->email->attach($file);
					}
				}
				
				$this->CI->email->subject($mail_subject);				
				$this->CI->email->message($body);								
				
				$result = $this->CI->email->send();
				
				if($debug || $this->smtp_active)
				{
					if(!$result){
						echo "<pre>";
						echo $this->CI->email->print_debugger();
						echo "</pre>";
					}
				}
				
				$this->CI->email->clear(TRUE);
				return $result;
			}
			return false;
		}
		return false;
	}

	public function mail_notify_sendgrid($mail_conf = array()){
		if(is_array($mail_conf) && !empty($mail_conf))
		{	   	 
			$mail_to            = $mail_conf['to_email'];
			$mail_subject       = $mail_conf['subject']; 
			$from_email         = 'mhdafjalit786@gmail.com';
			$from_name          = $mail_conf['from_name'];	
			$body               = $mail_conf['body_part'];				
			$file               = @$mail_conf['attachment'];
			if($file != '' && !is_array($file)){
				$file = array($file);
			}
			$cc                 = @$mail_conf['cc'];
			$bcc                = @$mail_conf['bcc'];
			$alternative_msg    = @$mail_conf['alternative_msg'];
			$debug              = @$mail_conf['debug'];

			if($mail_to != '')
			{
				$email = new \SendGrid\Mail\Mail();
				$email->setFrom($from_email, $from_name);
				$email->setReplyTo(
					new \SendGrid\Mail\ReplyTo(
						$from_email,
						$from_name
					)
				);
				$email->setSubject($mail_subject);
				$email->addTo($mail_to);
				
				if($cc != ''){
					$cc = $this->clean_email($this->_str_to_array($cc));
					if(!empty($cc)){
						foreach($cc as $ccval){
							$email->addCc($ccval);
						}
					}
				}
				
				if($bcc != ''){
					$bcc = $this->clean_email($this->_str_to_array($bcc));
					if(!empty($bcc)){
						foreach($bcc as $bccval){
							$email->addBcc($bccval);
						}
					}
				}
				
				$email->addContent("text/html", $body);
				
				if(is_array($file) && count($file) > 0)
				{
					foreach($file as $attach_path)
					{
						if($attach_path != '' && file_exists($attach_path))
						{
							if ( ! $fp = @fopen($attach_path, 'rb'))
							{
								if($debug){
									echo 'lang:email_attachment_unreadable: '.$attach_path;
									return FALSE;
								}
							}

							$file_content = stream_get_contents($fp);
							$mime = $this->_mime_types(pathinfo($attach_path, PATHINFO_EXTENSION));
							fclose($fp);
							$email_disposition = 'attachment';

							$email->addAttachment(
								base64_encode($file_content),
								$mime,
								basename($attach_path),
								$email_disposition
							);
						}
					}
				}
				
				// Replace with your actual SendGrid API key
				$sendgrid = new \SendGrid('YOUR_SENDGRID_API_KEY_HERE');
				try {
					$response = $sendgrid->send($email);
					if($debug){
						print $response->statusCode() . "\n";
						print_r($response->headers());
						print $response->body() . "\n";
					}
					return true;
				} catch (Exception $e) {
					if($debug){
						echo 'Caught exception: '. $e->getMessage() ."\n";
					}
					return false;
				}
			}
		}
		return false;
	}

	protected function _str_to_array($email)
	{
		if ( ! is_array($email))
		{
			return (strpos($email, ',') !== FALSE)
				? preg_split('/[\s,]/', $email, -1, PREG_SPLIT_NO_EMPTY)
				: (array) trim($email);
		}
		return $email;
	}

	public function clean_email($email)
	{
		if ( ! is_array($email))
		{
			return preg_match('/\<(.*)\>/', $email, $match) ? $match[1] : $email;
		}

		$clean_email = array();

		foreach ($email as $addy)
		{
			$clean_email[] = preg_match('/\<(.*)\>/', $addy, $match) ? $match[1] : $addy;
		}
		return $clean_email;
	}

	protected function _mime_types($ext = '')
	{
		$ext = strtolower($ext);
		$mimes =& get_mimes();

		if (isset($mimes[$ext]))
		{
			return is_array($mimes[$ext])
				? current($mimes[$ext])
				: $mimes[$ext];
		}
		return 'application/x-unknown-content-type';
	}
	
	public function is_smtp_active()
	{
		return $this->smtp_active;
	}
	
	public function set_smtp_active($active)
	{
		$this->smtp_active = $active;
		$this->x_use_smtp = $active ? 1 : 0;
		
		if($active){
			$this->configure_smtp_by_server();
		} else {
			$this->configure_local_mail();
		}
	}
	
	public function set_email_server($server)
	{
		$this->email_server = $server;
		if($this->smtp_active){
			$this->configure_smtp_by_server();
		}
	}
	
	public function get_email_server()
	{
		return $this->email_server;
	}
	
	public function is_live()
	{
		return $this->is_live_server;
	}
}
?>