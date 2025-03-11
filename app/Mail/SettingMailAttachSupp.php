<?php 
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SettingMailAttachSupp extends Mailable

{

    use Queueable, SerializesModels;

    public $details,$attach_file,$attach_name;
    
	/**

     * Create a new message instance.

     *

     * @return void

     */

    public function __construct($details,$attach_file,$attach_name)

    {

        $this->details = $details;
		$this->attach_file = $attach_file;
		$this->attach_name= $attach_name;
	
    }

   

    /**

     * Build the message.

     *

     * @return $this

     */

    public function build()

    {
      
    	$from  = $this->details["from"];
		$reply_to_name = $this->details["reply_to_name"];
		$reply_to_address = $this->details["reply_to_address"];
		$subject = $this->details["subject"];
		
		return $this->from(env('MAIL_FROM_ADDRESS'),$from)
					->replyTo($reply_to_address,$reply_to_name)
					->view('emails.send_supplier_view')
                    ->subject($subject)
					->bcc('dabtiehp@hotmail.fr', 'GhD')
					->bcc('hascheaito@hotmail.com', 'HCH')
					->attachData($this->attach_file, $this->attach_name)
					->with('details', $this->details);
	   
	    

                    
	}
	


}