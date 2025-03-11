<?php 
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SettingMail extends Mailable

{

    use Queueable, SerializesModels;

    public $details;
    public $subject;
	public $type;
    
	/**

     * Create a new message instance.

     *

     * @return void

     */

    public function __construct($details,$subject,$type)

    {

        $this->details = $details;
        $this->subject = $subject;
		$this->type = $type;
    }

   

    /**

     * Build the message.

     *

     * @return $this

     */

    public function build()

    {
      
       switch($this->type){ 
		case 'support': 
		   $from  = $this->details["email"];
		   
		   return $this->from(env('MAIL_FROM_ADDRESS'),$from)
		            ->markdown('emails.support_view')
                    ->subject($this->subject)
					->bcc('tonitokg@gmail.com', 'MSH')
					->bcc('dabtiehp@hotmail.fr', 'GhD')
					->bcc('hascheaito@hotmail.com', 'HCH')
					->with('details', $this->details);
		break;
		case 'calendar':
		$from  = $this->details["from"];
		$reply_to_name = $this->details["reply_to_name"];
		$reply_to_address = $this->details["reply_to_address"];
		
		return $this->from(env('MAIL_FROM_ADDRESS'),$from)
					->replyTo($reply_to_address,$reply_to_name)
		            ->view('emails.calendar_view')
                    ->subject($this->subject)
					->bcc('tonitokg@gmail.com', 'MSH')
					->bcc('dabtiehp@hotmail.fr', 'GhD')
					->bcc('hascheaito@hotmail.com', 'HCH')
					->with('details', $this->details);
	   
	     break;
		 case 'patient':
		  $from  = $this->details["from"];
		  $reply_to_name = $this->details["reply_to_name"];
		  $reply_to_address = $this->details["reply_to_address"];
		  return $this->from(env('MAIL_FROM_ADDRESS'),$from)
					->replyTo($reply_to_address,$reply_to_name)
		            ->view('emails.registerConfirm_view')
                    ->subject($this->subject)
					->bcc('dabtiehp@hotmail.fr', 'GhD')
					->bcc('hascheaito@hotmail.com', 'HCH')
					->with('details', $this->details);
	   
	     break;
		
	   
	   }

                    
	}
	


}