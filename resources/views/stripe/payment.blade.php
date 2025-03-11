@extends("gui.main_gui")
@section("styles")
 <style>
  .bg-inf{
	   background-color: #1bbc9b;
   }
  
 </style>
 
@endsection
@section("content")
  
   <div class="container-fluid">         
       <div class="row m-1">
		<section class="col-md-6 offset-md-3"> 
		         <div class="card">
			        <div class="card-header p-2">
					  
                            <h4>{{__('Online Payment')}}<span class="float-right m-1">
												<i class="fab fa-cc-visa fa-lg pr-1"></i>
												<i class="fab fa-cc-amex fa-lg pr-1"></i>
												<i class="fab fa-cc-mastercard fa-lg"></i>
												</span></h4>
                        
					</div>
					<div class="card-body p-1">
						@if (Session::has('success'))
						 <div class="alert alert-success text-center" style="background-color:#1bbc9b;">
							<p>{{ Session::get('success') }}</p>
							<p><a  href="{{route('patient_dash.index',app()->getLocale())}}" onclick="localStorage.setItem('dashTab', '#dashboard_stripe');">{{__('Click here to proceed to your payments')}}</a></p>
						 </div>
                        @endif
						<div class="form-group">
						   <div>     
							<legend class="border-bottom" style="font-size:1.2em;"><b>{{__('Appointment')}}</b></legend>
							<div><label class='control-label'>{{__('Exam Name').' : '.$CodeExam}}</label></div>
							<div><label class='control-label'>{{__('Date/Time').' : '.$ExamDate}}</label></div> 
						   </div>
						</div>
						<hr/>
						 <form action="{{ route('stripe.payment.pay',app()->getLocale()) }}" data-stripe-publishable-key="{{ env('STRIPE_KEY') }}" method="post" id="payment-form">
							@csrf	
								
								   
						            <div>     
							 		 <label class='control-label'>{{__('Please fill your card details').' :'}}</label> 
						            </div>
						          
								   <div class="p-2 border form-group" id="card-element"></div>
								   <div class="form-group badge bg-gradient-danger label-size" id="card-errors" role="alert alert-success"></div>
								   <div class="form-group text-center">
								      <button class="btn btn-action">{{__('Pay').' '.$PriceExam.' CAD '}}</button>
								    </div>	  
						   </form>
					    </div>	   
                  </div>
				</section>
              </div>
        </div>
		
 @endsection
 @section('scripts') 
 <script src="https://js.stripe.com/v3/"></script>
<script>

         var $form = $("#payment-form");
		 // Create a Stripe client
         var lang='{{app()->getLocale()}}';
		 var stripe = Stripe($form.data('stripe-publishable-key'),{locale: lang});

         // Create an instance of Elements
         var elements = stripe.elements();
         
		 
         // Custom styling can be passed to options when creating an Element.
         // (Note that this demo uses a wider set of styles than the guide below.)
         let styles = {
				iconStyle: 'solid',
				base: {
					color: '#32325D',
					fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
					fontSmoothing: 'antialiased',
					fontSize: '16px',
					'::placeholder': {
						color: '#AAB7C4'
					}
				},
				invalid: {
					color: '#CC0000',
					iconColor: '#FA755A'
				}
			};

     // Create an instance of the card Element
     let card = elements.create('card', {
			hidePostalCode: true,
			style: styles,
		});

      card.mount('#card-element');

     // Handle real-time validation errors from the card Element.
     card.addEventListener('change', function(event) {
         var displayError = document.getElementById('card-errors');
         if (event.error) {
             displayError.textContent = event.error.message;
         } else {
           displayError.textContent = '';
         }
     });

     // Handle form submission
     var form = document.getElementById('payment-form');
     form.addEventListener('submit', function(event) {
           event.preventDefault();
           stripe.createToken(card).then(function(result) {
           if (result.error) {
               // Inform the user if there was an error
               var errorElement = document.getElementById('card-errors');
               errorElement.textContent = result.error.message;
           } else {
              // Send the token to your server
              var token = result.token["id"];
			  console.log(token);
              $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
              $form.append("<input type='hidden' name='event_id' value='{{$event_id}}'>");
			  $form.get(0).submit();
           }
        });
    });
    </script>
 @endsection